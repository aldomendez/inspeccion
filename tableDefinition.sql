Create table fallas_Lr4 (
  serial_num varchar(12),
  status varchar(2),
  last_upd_date date default (sysdate),
  carrier_serial_num varchar(12),
  carrier_site varchar(3),
  user_id varchar(10),
  comments varchar(300),
  component varchar(20),
  failmode varchar(30)
);


INSERT INTO fallas_lr4 (
  serial_num,measure_date,carrier_serial_num,carrier_site,user_id,comments,componente,fail_mode
) values
(:serial_num,:measure_date,:carrier_serial_num,:carrier_site,:user_id,:comments,:componente,:fail_mode)

-- seleciona todas la columnas de a y b 
SELECT DISTINCT a.*, b.* 
FROM phase2.carrier_site@mxoptix a 
left OUTER JOIN apogee.fallas_lr4@mxapps b
ON a.serial_num = b.serial_num
WHERE a.carrier_serial_num = '155772978'       
ORDER BY a.carrier_site
