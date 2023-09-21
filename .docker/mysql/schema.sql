create table if not exists users
(
    id binary(16) not null,
    name varchar(128) not null,
    email varchar(320) not null,
    password varchar(128) not null,
    primary key (id),
    index (email)
);

create table if not exists roles
(
    id int not null auto_increment,
    name varchar(32) not null,
    primary key (id),
    unique index (name)
);
insert into roles (name) values ('admin'), ('analyst'), ('editor');

create table if not exists user_roles
(
    user_id binary(16) not null,
    role_id int not null,
    primary key (user_id, role_id),
    foreign key (user_id) references users (id) on delete cascade,
    foreign key (role_id) references roles (id) on delete cascade
);

create table if not exists image
(
    id binary(16) not null,
    name varchar(64) not null,
    format varchar(10) not null,
    size int unsigned not null,
    content mediumblob not null,
    primary key (id)
);

create table if not exists datasets
(
    id binary(16) not null,
    name varchar(64) not null,
    date datetime not null,
    num_images int unsigned not null,
    size int unsigned not null,
    primary key (id)
);

create table if not exists dataset_image
(
    dataset_id binary(16) not null,
    image_id binary(16) not null,
    primary key (dataset_id, image_id),
    foreign key (dataset_id) references datasets (id) on delete cascade,
    foreign key (image_id) references image (id) on delete cascade,
    index (dataset_id),
    index (image_id)
);

create table if not exists analysis
(
    id binary(16) not null,
    date datetime not null,
    target varchar(64),
    num_records int unsigned not null,
    num_panels int unsigned not null,
    num_hotspots int unsigned not null,
    primary key (id)
);

create table if not exists image_analysis
(
    analysis_id binary(16) not null,
    image_id binary(16) not null,
    image_name varchar(64) not null,
    latitude decimal(11, 8),
    longitude decimal(11, 8),
    num_panels int unsigned not null,
    num_hotspots int unsigned not null,
    primary key (analysis_id, image_id),
    foreign key (analysis_id) references analysis (id) on delete cascade,
    index (analysis_id),
    index (image_id)
);

create table if not exists panels
(
    id binary(16) not null,
    analysis_id binary(16) not null,
    image_id binary(16) not null,
    panel_index tinyint unsigned not null,
    score decimal(5, 4),
    x_min smallint unsigned not null,
    x_max smallint unsigned not null,
    y_min smallint unsigned not null,
    y_max smallint unsigned not null,
    primary key (id),
    index (analysis_id),
    index (analysis_id, image_id),
    foreign key (analysis_id, image_id) references image_analysis (analysis_id, image_id) on delete cascade
);

create table if not exists hotspots
(
    id binary(16) not null,
    panel_id binary(16) not null,
    hotspot_index tinyint unsigned not null,
    score decimal(5, 4),
    x_min smallint unsigned not null,
    x_max smallint unsigned not null,
    y_min smallint unsigned not null,
    y_max smallint unsigned not null,
    primary key (id),
    foreign key (panel_id) references panels (id) on delete cascade,
    index (panel_id)
);

create table if not exists output_image
(
    id binary(16) not null,
    analysis_id binary(16) not null,
    image_id binary(16),
    name varchar(64) not null,
    format varchar(10) not null,
    size int unsigned not null,
    content mediumblob not null,
    primary key (id),
    index (analysis_id, image_id, name),
    index (analysis_id, image_id),
    index (name)
);

create table if not exists output_csv
(
    id binary(16) not null,
    analysis_id binary(16) not null,
    image_id binary(16),
    name varchar(64) not null,
    size int unsigned not null,
    content mediumblob not null,
    primary key (id),
    index (analysis_id, image_id, name),
    index (analysis_id, image_id),
    index (name)
);
