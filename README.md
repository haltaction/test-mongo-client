##Setup:
- Install Docker & Docker Compose.
- Run `sudo docker-compose up -d`.
- Run `sudo docker ps` and copy CONTAINER ID `from mongoclient_nginx_1`.
- Run `sudo docker inspect container_ID`, where _container_ID_ - string from previous step.
- Find in "NetworkSettings"->"Networks"->"mongoclient_default"->"IPAddress" and copy it value.
- Add in your hosts file line `IP_Address local.mongo-cl.com` where IP_Address - string from previous step.

##Command format:
```
SELECT [<Projections>] [FROM <Target>]
[WHERE <Condition>*]
[ORDER BY <Fields>* [ASC|DESC] *]
[SKIP <SkipRecords>]
[LIMIT <MaxRecords>];
```
sdfdfs