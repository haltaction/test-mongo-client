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
where:
- _Projections_ - can be `\*, field, field.subfield, field.\*` without any aggregation and so on
- _Target_ - `collection_name` without any quotes
- _Condition_ - without sub-conditions, only linear, like `\[A\] AND \[B\] OR \[C\]`. 
Can use _AND_, _OR_ for logical combination, and `=, <>, >, >=, <, <=` operators for comparing fields with values
- _Fields_ - fields names, separated by coma, with type of sorting `ASC|DESC`
- _SkipRecords_ - number of record to skip
- _MaxRecords_ - number of records to show

All titles must be without quotes and other special characters.
