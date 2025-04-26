from locust import HttpUser, task, between

class EGoVUser(HttpUser):
    host = "http://localhost"
    wait_time = between(1, 3)

    def on_start(self):
        # Simulasi login
        response = self.client.post(
            "/php/e-gov/auth/login.php",
            data={
                "username": "khalid",
                "password": "123123"
            },
            allow_redirects=False  # Jangan ikuti redirect otomatis
        )
        # Periksa apakah login berhasil (misalnya redirect ke dashboard)
        if response.status_code == 302:
            print("Login berhasil, redirect ke:", response.headers.get("Location"))
        else:
            print("Login gagal, status code:", response.status_code)

    @task(1)
    def access_dashboard(self):
        response = self.client.get("/php/e-gov/admin/dashboard.php", allow_redirects=False)
        if response.status_code == 200:
            print("Dashboard accessed successfully")
        else:
            print("Dashboard failed, status code:", response.status_code)

    @task(2)
    def access_detail(self):
        response = self.client.get("/php/e-gov/user/detail.php?id=5", allow_redirects=False)
        if response.status_code == 200:
            print("Detail accessed successfully")
        else:
            print("Detail failed, status code:", response.status_code)

    @task(1)
    def access_index(self):
        response = self.client.get("/php/e-gov/user/index.php", allow_redirects=False)
        if response.status_code == 200:
            print("Index accessed successfully")
        else:
            print("Index failed, status code:", response.status_code)