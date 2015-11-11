'''
Created on 25-Sep-2015
@author: suhas
'''

import logging
from cassandra.cluster import Cluster

class configDB:
    cluster=None
    session=None
    log = None
    keyspace = None
    
    
    def __init__(self):
        logging.info("Setting up cassandra DB")

    """Connect to cassandra cluster"""
    def Connect(self, nodes):
        cluster = Cluster(nodes)
        metadata = cluster.metadata
        self.session = None
        self.session = cluster.connect()
        logging.info('Connected to cluster: ' + metadata.cluster_name)
        for host in metadata.all_hosts():
            logging.info('Datacenter: %s; Host: %s; Rack: %s', host.datacenter, host.address, host.rack)
        #self.session.execute("USE \"%s\"", self.keyspace)
    
    
    """Terminate Cassandra cluster connection"""
    def Close(self):
        self.session.cluster.shutdown()
        self.session.shutdown()

    """Executes Cassandra cqlsh query. Namespace is configDbKeyspace"""
    def Transaction(self,query):    
        result = self.session.execute(query);
        return result;
    
    def createNameSpace(self, keyspace):
        self.keyspace=keyspace
        query = "CREATE KEYSPACE "+keyspace+" WITH replication = {'class':'SimpleStrategy', 'replication_factor' : 3}"	
        self.Transaction(query)
        
    def createTable(self):
        self.Transaction("");


def main():
    logging.basicConfig(filename='/var/log/configDB.log', filemode='w', format='%(asctime)-15s:%(levelname)s:%(message)s', 
                        level=logging.DEBUG)
    
    db = configDB()
    db.Connect(["127.0.0.1"])
    db.createNameSpace("configDbKeyspace")
    
if __name__ == '__main__':
    main()
