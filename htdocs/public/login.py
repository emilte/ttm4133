import requests

def logout(grp=None, a=10, b=44, verbose=True):
    if grp:
        a, b = grp, grp

    for i in range(a, b+1):
        url = "https://grp{0}.ttm4135.item.ntnu.no:90{0}/logout".format(i)
        try:
            r = requests.get(url=url)
            if verbose:
                print("Logged out from grp{}".format(i))
        except:
            print("Error at grp{}".format(i))

def login(username="admin", password="admin", grp=None, a=10, b=44, verbose=True):
    if grp:
        a, b = grp, grp

    for i in range(a, b+1):
        url = "https://grp{0}.ttm4135.item.ntnu.no:90{0}/login".format(i)
        try:
            r = requests.post(url=url, data={
                'username': username,
                'password': password,
                'password2': password,
            })
            if verbose:
                if "logout" in r.text:
                    print("Grp{} has default admin".format(i))
        except:
            print("Error at grp{}".format(i))


for i in range(20):
    print(i)
    login(grp=19)
    logout(grp=19)
