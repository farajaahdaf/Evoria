Fitur Platform Mobile
	Platform mobile merupakan platform utama untuk attendee atau pengguna umum. Aplikasi dikembangkan menggunakan Flutter dan berkomunikasi dengan backend Laravel melalui REST API.
	
	4.2.1 User / Attendee
	1. Login dan Registrasi
		User dapat membuat akun dan login untuk melakukan pembelian tiket.

	2. Daftar Event
User dapat melihat daftar event yang tersedia dan melakukan pencarian atau filter berdasarkan:
Kategori
Kota
Tanggal
Harga tiket

		3. Detail Event
Menampilkan informasi lengkap event, termasuk deskripsi, jadwal, lokasi, peta, dan jenis tiket yang tersedia.

		4. Checkout dan Pembayaran
User dapat memilih tiket, melakukan checkout, lalu diarahkan ke sistem pembayaran Midtrans Sandbox.
		5. Riwayat Pesanan
			User dapat melihat daftar pembelian tiket yang pernah dilakukan.
		6. E-Ticket
			User dapat melihat e-ticket yang dimiliki lengkap dengan QR Code.

		7. Maps / Geolocation
Aplikasi dapat menampilkan lokasi event melalui integrasi Google Maps. Fitur ini dapat membantu user mengetahui lokasi event dengan lebih mudah.

		8. Chatbot AI Event Assistant
User dapat mengajukan pertanyaan kepada AI Event Assistant, misalnya:
“Ada konser di Bandung minggu ini?”
“Seminar teknologi di Jakarta?”
“Event murah akhir pekan ini?”
Sistem akan melakukan pre-filter berdasarkan data event di database, kemudian menggunakan OpenAI API untuk menyusun rekomendasi yang relevan.

		9. Notifikasi
			Sistem dapat memberikan notifikasi terkait:
Pembayaran berhasil
Event yang sudah disetujui
Pengingat event mendekati hari pelaksanaan
