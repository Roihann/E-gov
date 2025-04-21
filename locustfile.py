from locust import HttpUser, task, between

class EGoVUser(HttpUser):
    # Host yang akan diuji
    host = "http://localhost"

    # Jeda antar tindakan pengguna (1-3 detik, simulasi perilaku nyata)
    wait_time = between(1, 3)

    def on_start(self):
        # Simulasi login saat pengguna mulai
        self.client.post(
            "/php/e-gov/auth/login.php",
            data={
                "username": "rehan",  # Ganti dengan username yang valid di database
                "password": "rehan123"  # Ganti dengan password yang valid di database
            }
        )

    @task(1)
    def access_dashboard(self):
        # Mengakses halaman dashboard setelah login
        self.client.get("/php/e-gov/dashboard.php")

    @task(2)
    def access_detail(self):
        # Mengakses halaman detail wisata (ganti ID sesuai data di database)
        self.client.get("/php/e-gov/detail.php?id=1")

    @task(1)
    def access_index(self):
        # Mengakses halaman index
        self.client.get("/php/e-gov/index.php")