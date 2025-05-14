create table if not exists products
(
    id int auto_increment primary key,
    uuid  varchar(255) not null comment 'UUID товара',
    category  varchar(255) not null comment 'Категория товара',
    is_active tinyint default 1  not null comment 'Флаг активности',
    name text default '' not null comment 'Тип услуги',
    description text null comment 'Описание товара',
    thumbnail  varchar(255) null comment 'Ссылка на картинку',
    price float not null comment 'Цена'
)
    comment 'Товары';

CREATE UNIQUE INDEX uuid_unique_idx ON products (uuid);
CREATE INDEX is_active_category_idx ON products (is_active, category);
