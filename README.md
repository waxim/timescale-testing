# Flux - DB Write testing tool

Currently only tests against the default `conditions` test table which must be seeded first, you can seed it from the the `setup.sql` file in `scripts/`

# Setup
```
$ docker run -d --name timescaledb -p 5432:5432 -e POSTGRES_PASSWORD=password timescale/timescaledb
```

Then

```
$ composer install
$ php flux fill --table=condtitions --rows=1000000
```
