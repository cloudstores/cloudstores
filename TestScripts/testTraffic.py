'''
Created on 23-Sep-2015

@author: suhas
'''
import time
import subprocess

def main():
    testId=raw_input(">")
    count=0
    start = time.time()
    for i in range(int(testId)):
        output = subprocess.check_output("curl http://127.0.0.1:8080", shell=True)
        print output
        if(output=='<h1>Hello World</h1>'):
            count=count+1
    end = time.time()
    
    if count==int(testId):
        print "Traffic tested for:",testId," successful in time diff:",(end-start)
    else:
        print "Traffic tested for:",testId," successful in time diff:",(end-start)
        


if __name__=='__main__':
    main()