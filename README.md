# Norse Digital test task


**How to run this project using docker:**
- Clone repository
- Go into the project folder
- Run `docker-compose -f docker-compose.prod.yml up -d --build`
- That's all! Open [http://localhost:8082/en/](http://localhost:8082/en/)


_P.S. Default ports for prod are: 8082 (nginx) and 5002 (mongodb). You can change them if thay are busy in your system_



**Logs**

You can found logs in the "shared/logs" folder (web-server, cron, etc.)