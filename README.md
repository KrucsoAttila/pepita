
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
A seedek és a migrációk alapján működő adatbázis jön létre

