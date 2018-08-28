import requests
import json
import argparse
import pprint

class SalsahError(Exception):
    """Error in getting data from SALSAH"""
    pass

class Salsah:
    """
    Class to gather data from SALSAH
    """
    def __init__(self, server, user, password):
        """
        Constructor requireing the server adress, the user and password of SALSAH
        :param server: Adress of the serer, e.g http://data.dasch.swiss
        :param user: Username for SALSAH
        :param password: The password
        """
        self.server = server
        self.user = user
        self.password = password

    def get_all_obj_ids(self, project, start_at=0, show_nrows=-1):
        """
        Gets all objects ID's of a given project
        :param project: SALSAH Name of the project
        :return: List of all object (resource) ID's
        """
        payload = {
            'searchtype' : 'extended',
            'filter_by_project' : project
        }

        if nrows > 0:
            payload['show_nrows'] = show_nrows
            payload['start_at'] = start_at

        req = requests.get(self.server + '/api/search/', params=payload, auth=(self.user, self.password))
        result = req.json()
        if result['status'] != 0:
            raise SalsahError("SALSAH-ERROR:\n" + result['errormsg'])

        else:
            obj_ids = list(map(lambda a: a['obj_id'], result['subjects']))

            return obj_ids

    def get_resource(self, res_id):
        req = requests.get(self.server + '/api/resources/' + res_id, auth=(self.user, self.password))
        result = req.json()
        if result['status'] != 0:
            raise SalsahError("SALSAH-ERROR:\n" + result['errormsg'])

        else:
            return result


parser = argparse.ArgumentParser()
parser.add_argument("server", help="URL of the SALSAH server")
parser.add_argument("-u", "--user", help="Username for SALSAH")
parser.add_argument("-p", "--password", help="The password for login")
parser.add_argument("-n", "--nrows", type=int, help="number of records to get, -1 to get all")
parser.add_argument("-s", "--start", type=int, help="Start at record with given number")
args = parser.parse_args()

user = 'root' if args.user is None else args.user
password = 'SieuPfa15' if args.password is None else args.password
start = args.start;
nrows = -1 if args.nrows is None else args.nrows

con = Salsah(args.server, user, password)
res_ids = con.get_all_obj_ids('postcards', start, nrows)
resources = list(map(con.get_resource, res_ids))

pprint.pprint(resources)


