# Norse Digital test task

##Requirements

- docker
- docker-compose version **1.13.0+** (for comfortable running containers)

##Installation
**How to run this project using docker:**
- Clone repository
- Go into the project folder
- Run `docker-compose -f docker-compose.prod.yml up -d --build`
- That's all! Open [http://localhost:8082/en/](http://localhost:8082/en/)


_P.S. Default ports for prod are: 8082 (nginx) and 5002 (mongodb). You can change them if thay are busy in your system_


##Using
**Default user:**

- email: **admin@gmail.com**
- password: **111**


##Development

**Docker info:**

- Build images: `docker-compose build`
- Run containers: `docker-compose up -d`
- Go inside container: `docker exec -ti norse_app bash`
- Add permission for the runtime folder (only first time): `mkdir -p web/runtime && chown -R 33:33 web/runtime`
- Load default fixtures (only first time): `mongorestore -d norse dump/norse/`
- Enable/disable xdebug (inside app container): `xdebug`

**Logs**

You can found logs in the "shared/logs" folder (web-server, cron, etc.)

