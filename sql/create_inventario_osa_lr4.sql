CREATE TABLE inventario_osa_lr4 (
 id NUMBER(9) NOT NULL,
 date_received DATE NOT NULL,
 date_added DATE DEFAULT (SYSDATE),
 carrier_site NUMBER(2),
 serial_num NUMBER(9),
 carrier_serial_num NUMBER(9),
 status VARCHAR2(50),
 db_status VARCHAR2(50),
 osfm_item VARCHAR2(50),
 comments VARCHAR2(140)
)