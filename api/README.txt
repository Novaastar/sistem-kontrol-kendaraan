API DRIVER
Base URL: http://localhost/sistem_kontrol_kendaraan_bank_jatim/api/

POST login.php
JSON: {"username":"driver01","password":"driver123"}

GET me.php
Header: Authorization: Bearer TOKEN

GET penugasan.php
Header: Authorization: Bearer TOKEN

POST update_perjalanan.php
Header: Authorization: Bearer TOKEN
JSON contoh mulai:
{"penugasan_id":1,"jam_ambil":"07:00","km_awal":12450}
JSON contoh selesai:
{"penugasan_id":1,"jam_pulang":"16:30","km_akhir":12520,"keterangan":"Selesai"}
