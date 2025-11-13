
## Deploy

Az első lépés hogy le kell clone-ozni a git repositoryt:
```bash
$ git clone https://github.com/KrucsoAttila/pepita.git
```
Belépni a gyökérbe:
```bash
$ cd pepita
```
Majd a project gyökerében indítani:
```bash
$ docker compose up -d --build
```

A parancs hibátlan lefutása után feláll a rendszer. A build készít egy Elasticsearch, Postgres, és egy Laravel konténert.  
Átmásolja a laravel project `.env.example` fájt a `.env` fájlba. Itt minden paraméter rendelkezésre áll a futáshoz. Ha  más a cél környezet akkor értelem szerűen szerkeszteni kell az `.env` fájlt.  
Ha mégsem futna le a `composer install` vagy az adatbázis nem állna fel vagy nem töltődnének be az adatok akkor a megfeleleő `artisan` vagy `composer` parancsot kell futtatni.  
A seedek és a migrációk alapján működő adatbázis jön létre.  


## Api Platform 

Az api dokumentációt az Api Platform ezen a routingon publikálja: 
```bash
 http://localhost:8080/api/docs
```

## Alias

A keresés tesztelés előtt az Elasticsearchben létre kell hozni kézzel az aliaszokat:
```bash
curl -sS -X POST http://es:9200/_aliases -H 'Content-Type: application/json' -d "{
  \"actions\": [
    {\"add\": {\"index\": \"products_v1\", \"alias\": \"product_read\"}},
    {\"add\": {\"index\": \"products_v1\", \"alias\": \"product_write\"}}
  ]
}"
```