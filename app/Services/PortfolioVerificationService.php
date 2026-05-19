<?php

namespace App\Services;

use App\Models\OrganizerPortfolioReview;
use App\Models\OrganizerProfile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use ZipArchive;

class PortfolioVerificationService
{
    private const TEMPLATE_VERSION = '1.0';

    public function analyze(OrganizerProfile $organizer): OrganizerPortfolioReview
    {
        $path = $organizer->portfolio_path;

        if (! $path || ! Storage::disk('public')->exists($path)) {
            return $this->storeReview($organizer, [
                'portfolio_path' => $path,
                'score' => 0,
                'risk_level' => 'Incomplete',
                'breakdown' => $this->emptyBreakdown(),
                'findings' => ['Portfolio file is missing.'],
                'extracted_text' => null,
                'error_message' => 'Portfolio file is missing.',
            ]);
        }

        try {
            $absolutePath = Storage::disk('public')->path($path);
            $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

            if ($extension !== 'docx') {
                return $this->storeReview($organizer, [
                    'portfolio_path' => $path,
                    'score' => 0,
                    'risk_level' => 'Incomplete',
                    'breakdown' => $this->emptyBreakdown(),
                    'findings' => ['Automated portfolio verification currently supports Evoria DOCX templates only.'],
                    'extracted_text' => null,
                    'error_message' => 'Unsupported automated verification file type.',
                ]);
            }

            $text = $this->extractDocxText($absolutePath);
            $result = $this->score($text, $organizer);

            return $this->storeReview($organizer, [
                'portfolio_path' => $path,
                'score' => $result['score'],
                'risk_level' => $result['risk_level'],
                'breakdown' => $result['breakdown'],
                'findings' => $result['findings'],
                'extracted_text' => $text,
                'error_message' => null,
            ]);
        } catch (\Throwable $exception) {
            return $this->storeReview($organizer, [
                'portfolio_path' => $path,
                'score' => 0,
                'risk_level' => 'Incomplete',
                'breakdown' => $this->emptyBreakdown(),
                'findings' => ['Portfolio could not be analyzed automatically.'],
                'extracted_text' => null,
                'error_message' => $exception->getMessage(),
            ]);
        }
    }

    private function extractDocxText(string $path): string
    {
        $zip = new ZipArchive();

        if ($zip->open($path) !== true) {
            throw new RuntimeException('Unable to open DOCX file.');
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (! $xml) {
            throw new RuntimeException('Invalid DOCX structure.');
        }

        $xml = preg_replace('/<\/w:p>/', "\n", $xml);
        $xml = preg_replace('/<w:tab\/>/', ' ', $xml);
        $xml = preg_replace('/<w:br\/>/', "\n", $xml);

        preg_match_all('/<w:t[^>]*>(.*?)<\/w:t>/s', $xml, $matches);

        $text = collect($matches[1] ?? [])
            ->map(fn ($value) => html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_XML1, 'UTF-8'))
            ->implode(' ');

        return trim(preg_replace('/[ \t]+/', ' ', preg_replace('/\n+/', "\n", $text)));
    }

    private function score(string $text, OrganizerProfile $organizer): array
    {
        $sections = $this->sections($text);
        $findings = [];

        $identity = $this->scoreFields('Organization Identity', 25, $sections[1] ?? '', [
            'company_name' => ['Nama Perusahaan / Komunitas', ['Jenis Organisasi']],
            'organization_type' => ['Jenis Organisasi', ['Tahun Berdiri']],
            'founded_year' => ['Tahun Berdiri', ['NPWP']],
            'address' => ['Alamat Lengkap', ['Kota / Kabupaten']],
            'city' => ['Kota / Kabupaten', ['Provinsi']],
            'province' => ['Provinsi', ['Website']],
        ], $findings);

        $pic = $this->scoreFields('Person In Charge', 20, $sections[2] ?? '', [
            'pic_name' => ['Nama Lengkap PIC', ['Jabatan / Posisi']],
            'position' => ['Jabatan / Posisi', ['Nomor HP / WA']],
            'phone' => ['Nomor HP / WA', ['Email']],
            'email' => ['Email', ['Nomor KTP']],
        ], $findings);

        $profile = $this->scoreProfile($sections[3] ?? '', $findings);
        $experience = $this->scoreExperience($sections[4] ?? '', $sections[5] ?? '', $findings);
        $plan = $this->scoreFields('Evoria Event Plan', 15, $sections[5] ?? '', [
            'event_title' => ['Judul Event', ['Kategori Event']],
            'event_category' => ['Kategori Event', ['Perkiraan Tanggal Pelaksanaan']],
            'event_date' => ['Perkiraan Tanggal Pelaksanaan', ['Perkiraan Lokasi / Venue']],
            'venue' => ['Perkiraan Lokasi / Venue', ['Perkiraan Jumlah Peserta']],
            'participants' => ['Perkiraan Jumlah Peserta', ['Kisaran Harga Tiket']],
            'event_description' => ['Deskripsi Singkat Event', ['BAGIAN 6']],
        ], $findings);

        $breakdown = [
            'identity' => $identity,
            'pic' => $pic,
            'profile' => $profile,
            'experience' => $experience,
            'event_plan' => $plan,
        ];

        $score = collect($breakdown)->sum('score');
        $score = max(0, min(100, (int) round($score)));

        $this->addCrossChecks($organizer, $pic, $findings);

        return [
            'score' => $score,
            'risk_level' => $this->riskLevel($score),
            'breakdown' => $breakdown,
            'findings' => array_values(array_unique($findings)),
        ];
    }

    private function scoreFields(string $label, int $maxScore, string $section, array $fields, array &$findings): array
    {
        $valid = 0;
        $fieldResults = [];

        foreach ($fields as $key => [$fieldLabel, $nextLabels]) {
            $value = $this->fieldValue($section, $fieldLabel, $nextLabels);
            $isValid = $this->isFilled($value);

            if ($isValid && $key === 'email') {
                $isValid = filter_var($this->firstEmail($value), FILTER_VALIDATE_EMAIL) !== false;
            }

            if ($isValid && $key === 'phone') {
                $isValid = preg_match('/^(?:0|62)?8\d{7,12}$/', preg_replace('/\D+/', '', $value)) === 1;
            }

            if ($isValid && str_contains($key, 'year')) {
                $isValid = preg_match('/\b(19|20)\d{2}\b/', $value) === 1;
            }

            if ($isValid) {
                $valid++;
            } else {
                $findings[] = "{$label}: {$fieldLabel} is missing or invalid.";
            }

            $fieldResults[$key] = [
                'label' => $fieldLabel,
                'value' => $this->preview($value),
                'valid' => $isValid,
            ];
        }

        return [
            'label' => $label,
            'score' => (int) round(($valid / count($fields)) * $maxScore),
            'max_score' => $maxScore,
            'valid_fields' => $valid,
            'total_fields' => count($fields),
            'fields' => $fieldResults,
        ];
    }

    private function scoreProfile(string $section, array &$findings): array
    {
        $description = $this->fieldValue($section, 'Deskripsi Organisasi', ['Visi & Misi', 'Visi']);
        $wordCount = str_word_count(strip_tags($description));
        $score = 0;

        if ($wordCount >= 100) {
            $score = 20;
        } elseif ($wordCount >= 50) {
            $score = 12;
            $findings[] = "Organization Profile: description has {$wordCount} words; minimum recommended is 100.";
        } elseif ($this->isFilled($description)) {
            $score = 6;
            $findings[] = "Organization Profile: description is too short ({$wordCount} words).";
        } else {
            $findings[] = 'Organization Profile: organization description is missing.';
        }

        return [
            'label' => 'Organization Profile',
            'score' => $score,
            'max_score' => 20,
            'word_count' => $wordCount,
            'fields' => [
                'description' => [
                    'label' => 'Deskripsi Organisasi',
                    'value' => $this->preview($description),
                    'valid' => $wordCount >= 100,
                ],
            ],
        ];
    }

    private function scoreExperience(string $section, string $planSection, array &$findings): array
    {
        $score = 0;

        $experience = $this->fieldValue($section, 'Lama Pengalaman di Bidang Event', ['Total Event']);
        $totalEvents = $this->fieldValue($section, 'Total Event yang Pernah Diselenggarakan', ['Jenis Event']);
        $eventTypes = $this->fieldValue($section, 'Jenis Event yang Biasa Digeluti', ['Kapasitas Peserta Terbesar']);
        $largestCapacity = $this->numberFrom($this->fieldValue($section, 'Kapasitas Peserta Terbesar', ['Kota-kota']));
        $trackRecord = $this->trackRecordArea($section);
        $validTrackRecords = $this->countValidTrackRecords($trackRecord);

        if ($this->numberFrom($experience) > 0) {
            $score += 3;
        } else {
            $findings[] = 'Event Experience: experience duration is missing or invalid.';
        }

        $claimedEvents = $this->numberFrom($totalEvents);
        if ($claimedEvents > 0) {
            $score += 3;
        } else {
            $findings[] = 'Event Experience: total past events is missing or invalid.';
        }

        if ($this->isFilled($eventTypes)) {
            $score += 2;
        } else {
            $findings[] = 'Event Experience: event type specialization is missing.';
        }

        if ($validTrackRecords >= 3) {
            $score += 8;
        } elseif ($validTrackRecords > 0) {
            $score += max(2, $validTrackRecords * 2);
            $findings[] = "Event Experience: only {$validTrackRecords} valid track record entries found; minimum is 3.";
        } else {
            $findings[] = 'Event Experience: no valid track record entries found.';
        }

        $consistencyScore = 4;
        if ($claimedEvents > 0 && $validTrackRecords > 0 && $claimedEvents >= 10 && $validTrackRecords < 3) {
            $consistencyScore -= 2;
            $findings[] = "Fraud risk indicator: organizer claims {$claimedEvents} past events but provides only {$validTrackRecords} valid track record entries.";
        }

        $plannedParticipants = $this->numberFrom($this->fieldValue($planSection, 'Perkiraan Jumlah Peserta', ['Kisaran Harga Tiket']));
        if ($largestCapacity > 0 && $plannedParticipants > ($largestCapacity * 5)) {
            $consistencyScore -= 2;
            $findings[] = "Fraud risk indicator: planned participant capacity ({$plannedParticipants}) is far above documented largest capacity ({$largestCapacity}).";
        }

        $score += max(0, $consistencyScore);

        return [
            'label' => 'Event Experience',
            'score' => min(20, $score),
            'max_score' => 20,
            'valid_track_records' => $validTrackRecords,
            'claimed_total_events' => $claimedEvents,
            'largest_capacity' => $largestCapacity,
            'planned_participants' => $plannedParticipants,
        ];
    }

    private function addCrossChecks(OrganizerProfile $organizer, array $pic, array &$findings): void
    {
        $email = $pic['fields']['email']['value'] ?? null;
        $extractedEmail = $this->firstEmail((string) $email);
        $accountEmail = strtolower((string) $organizer->user?->email);

        if ($extractedEmail && $accountEmail && strtolower($extractedEmail) !== $accountEmail) {
            $findings[] = 'Fraud risk indicator: PIC email does not match the Evoria account email.';
        }
    }

    private function sections(string $text): array
    {
        return [
            1 => $this->between($text, 'BAGIAN 1', 'BAGIAN 2'),
            2 => $this->between($text, 'BAGIAN 2', 'BAGIAN 3'),
            3 => $this->between($text, 'BAGIAN 3', 'BAGIAN 4'),
            4 => $this->between($text, 'BAGIAN 4', 'BAGIAN 5'),
            5 => $this->between($text, 'BAGIAN 5', 'BAGIAN 6'),
        ];
    }

    private function between(string $text, string $start, string $end): string
    {
        $startPos = mb_stripos($text, $start);
        if ($startPos === false) {
            return '';
        }

        $endPos = mb_stripos($text, $end, $startPos + mb_strlen($start));

        return trim(mb_substr($text, $startPos, $endPos === false ? null : $endPos - $startPos));
    }

    private function fieldValue(string $section, string $label, array $nextLabels): string
    {
        $start = mb_stripos($section, $label);
        if ($start === false) {
            return '';
        }

        $valueStart = $start + mb_strlen($label);
        $valueEnd = null;

        foreach ($nextLabels as $nextLabel) {
            $next = mb_stripos($section, $nextLabel, $valueStart);
            if ($next !== false && ($valueEnd === null || $next < $valueEnd)) {
                $valueEnd = $next;
            }
        }

        $value = mb_substr($section, $valueStart, $valueEnd === null ? null : $valueEnd - $valueStart);

        return $this->cleanValue($value);
    }

    private function cleanValue(string $value): string
    {
        $value = preg_replace('/\s+/', ' ', trim($value));
        $value = preg_replace('/^\*\s*/', '', $value);
        $value = preg_replace('/Contoh:\s*[^*]+/iu', '', $value);
        $value = preg_replace('/Harap diisi.+$/iu', '', $value);

        return trim($value, " \t\n\r\0\x0B:-");
    }

    private function isFilled(string $value): bool
    {
        $value = trim($value);

        if ($value === '' || mb_strlen($value) < 2) {
            return false;
        }

        $invalid = [
            'pt / cv / yayasan / komunitas / perorangan',
            'konser / seminar / festival / olahraga / pameran / lainnya',
            'jalan, nomor, rt/rw, kelurahan, kecamatan',
            'url profil media sosial lainnya',
            'sesuai ktp',
        ];

        return ! in_array(mb_strtolower($value), $invalid, true);
    }

    private function trackRecordArea(string $section): string
    {
        $start = mb_stripos($section, 'No Nama Event Tahun Kota Jml Peserta');

        return $start === false ? '' : mb_substr($section, $start);
    }

    private function countValidTrackRecords(string $text): int
    {
        preg_match_all('/\b(19|20)\d{2}\b.{0,100}?\b\d{2,7}\b/u', $text, $matches);

        return count($matches[0] ?? []);
    }

    private function numberFrom(string $value): int
    {
        if (! preg_match('/\d[\d\.,]*/', $value, $match)) {
            return 0;
        }

        return (int) preg_replace('/\D+/', '', $match[0]);
    }

    private function firstEmail(string $value): ?string
    {
        return preg_match('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $value, $match)
            ? strtolower($match[0])
            : null;
    }

    private function riskLevel(int $score): string
    {
        return match (true) {
            $score >= 85 => 'Strong Portfolio',
            $score >= 70 => 'Needs Manual Review',
            $score >= 50 => 'High Risk',
            default => 'Incomplete',
        };
    }

    private function emptyBreakdown(): array
    {
        return [
            'identity' => ['label' => 'Organization Identity', 'score' => 0, 'max_score' => 25],
            'pic' => ['label' => 'Person In Charge', 'score' => 0, 'max_score' => 20],
            'profile' => ['label' => 'Organization Profile', 'score' => 0, 'max_score' => 20],
            'experience' => ['label' => 'Event Experience', 'score' => 0, 'max_score' => 20],
            'event_plan' => ['label' => 'Evoria Event Plan', 'score' => 0, 'max_score' => 15],
        ];
    }

    private function preview(string $value): string
    {
        return mb_substr(trim($value), 0, 160);
    }

    private function storeReview(OrganizerProfile $organizer, array $data): OrganizerPortfolioReview
    {
        return $organizer->portfolioReviews()->create($data + [
            'template_version' => self::TEMPLATE_VERSION,
            'analyzed_at' => now(),
        ]);
    }
}
