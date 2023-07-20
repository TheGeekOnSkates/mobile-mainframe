import requests

s = requests.Session()
url = "http://localhost/mobile-mainframe.php"
r = s.post(url)
token = r.text
while True:
	print("> ", end="")
	cmd = input()
	if cmd == "exit":
		break
	r = s.post(url, data={"c": cmd, "t": token})
	print(r.text, end="")
