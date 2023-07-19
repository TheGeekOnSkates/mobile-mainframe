import requests
url = "http://104.200.17.247/apps/mm/index.php"
r = requests.post(url, data={"awesome": "It works!" })
print(r.text)
