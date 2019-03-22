from sys import executable
from subprocess import Popen, CREATE_NEW_CONSOLE
import threading

def f():
    Popen([executable, 'login.py'], creationflags=CREATE_NEW_CONSOLE)


for x in range(30):
    threading.Thread(target=f).start()
