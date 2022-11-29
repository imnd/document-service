CREATE SEQUENCE "public"."users_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

CREATE SEQUENCE "public"."user_mobile_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

CREATE TABLE "public"."users" (
  "id" int4 NOT NULL DEFAULT nextval('users_id_seq'::regclass),
  "operator_id" int4,
  "is_admin" bool NOT NULL DEFAULT false,
  "is_activated" bool NOT NULL DEFAULT false,
  "is_allow" bool NOT NULL DEFAULT true,
  "lastname" varchar(255) COLLATE "pg_catalog"."default",
  "firstname" varchar(255) COLLATE "pg_catalog"."default",
  "middlename" varchar(255) COLLATE "pg_catalog"."default",
  "email" varchar(255) COLLATE "pg_catalog"."default" NOT NULL,
  "email_verified_at" timestamp(0),
  "password" varchar(255) COLLATE "pg_catalog"."default",
  "hash" varchar(256) COLLATE "pg_catalog"."default",
  "hash_at" timestamp(0),
  "hash_permanent" varchar(256) COLLATE "pg_catalog"."default",
  "remember_token" varchar(100) COLLATE "pg_catalog"."default",
  "api_token" char(80),
  "created_at" timestamp(0),
  "updated_at" timestamp(0),
  "deleted_at" timestamp(0),
  "payload" jsonb
);

SELECT setval('"public"."user_mobile_id_seq"', 3, true);
ALTER SEQUENCE "public"."users_id_seq"
OWNED BY "public"."users"."id";
SELECT setval('"public"."users_id_seq"', 3, true);