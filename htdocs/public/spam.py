import requests

url = "https://grp43.ttm4135.item.ntnu.no:9043/register"

requests.post(url=url, data={'username': 'spam', 'password': 'Django123'})
