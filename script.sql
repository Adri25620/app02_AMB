create database app02_amb

create table clientes(
cli_id serial primary key,
cli_nombre varchar(100),
cli_apellidos varchar(100),
cli_telefono integer,
cli_situacion char(1)
);

create table productos(
pro_id serial primary key,
pro_nombre varchar(150),
pro_precio decimal(8,2),
pro_disponible integer,
pro_situacion char(1)
);

create table venta(
ven_id serial primary key,
ven_cliente integer,
ven_producto integer,
ven_cantidad integer,
ven_situacion char(1)
);


alter table venta add constraint (foreign key(ven_cliente)
references clientes(cli_id) constraint fk_ven_cli)

alter table venta add constraint (foreign key(ven_producto)
references productos(pro_id) constraint fk_ven_pro)