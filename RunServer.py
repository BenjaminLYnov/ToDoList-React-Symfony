import os

absFilePath = os.path.abspath(__file__)
path, filename = os.path.split(absFilePath)
os.system("cd " + path + " && symfony server:start")
input('\n\n\n\n\nAppuyer sur Entrée pour fermer...')