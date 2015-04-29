Create table fallas_Lr4 (
  serial_num varchar(12),
  measure_date date default (sysdate),
  carrier_serial_num varchar(12),
  carrier_site varchar(3),
  user_id varchar(10),
  comments varchar(300),
  componente varchar(20),
  fail_mode varchar(30)
);

INSERT INTO fallas_lr4 (
  serial_num,measure_date,carrier_serial_num,carrier_site,user_id,comments,componente,fail_mode
) values
(:serial_num,:measure_date,:carrier_serial_num,:carrier_site,:user_id,:comments,:componente,:fail_mode)