import requests

def main():
	# Get the URL we're connecting to
	print("\033cMOBILE MAINFRAME 1.0\r\n\r\nEnter address:  > ", end="")
	url = input()
	if url == "":
		print("URL can't be blank/empty.")
		return
	
	# Get the token
	s = requests.Session()
	r = s.post(url)
	token = r.text
	
	# Get the welcome message
	s = requests.Session()
	r = s.post(url, data={"i": "::welcome::", "t": token})
	print(r.text, end="")
	
	# And start the main loop
	while True:
		
		# Get the user's input
		print("\n> ", end="")
		cmd = input()
		
		# If the user wants out, tell the server and peace out
		if cmd == "exit":
			r = s.post(url, data={"i": "::end-session::", "t": token})
			print(r.text)
			print("Connection closed.")
			return
		
		# Otherwise, send the input and print the server's response
		r = s.post(url, data={"i": cmd, "t": token})
		print(r.text, end="")

if __name__ == "__main__": main()
