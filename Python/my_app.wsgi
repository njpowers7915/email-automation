import sys
#sys.path.insert(0, "/var/www/myfirstapp")

import logging
logging.basicConfig(stream=sys.stderr)

#sys.path.insert(0,"main.py")
from main import app as application
