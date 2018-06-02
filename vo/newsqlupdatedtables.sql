---new talbe for auto changing the new registration prefix , after auto qualification of Bu's the qualification file named qualify_pioneers.php
CREATE TABLE IF NOT EXISTS `prefix` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prefix` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `prefix` (`id`, `prefix`, `date`, `notes`) VALUES
(1, 'PA', '2016-01-01', 'Pioneers');

ALTER TABLE  `shop_order_line` ADD  `product_id` INT NOT NULL;

----col added to bu table for recording current step starts 0 to 4 then reset after cycle and add 20 rpts
ALTER TABLE  `bu` ADD  `curr_step` INT NOT NULL

-----query for creating new counter for points -- used in closure of day
ALTER TABLE  `bu` ADD  `Rcount` INT NOT NULL DEFAULT  '0',
ADD  `Lcount` INT NOT NULL DEFAULT  '0'

-----reset daily counter befor qulification
UPDATE dc SET `left_dc`=0, `right_dc`=0  WHERE `date` = CURDATE();

----- reset Dynamic BV for all irs , before qulificaion
UPDATE `bu` SET `left_dbv`=0,`right_dbv`=0 WHERE 1;

----reset wallet , dcpt, rpts,
UPDATE `ir` SET `ewallet`=0,`total_ewallet`=0,`dcpts`=0,`total_dcpts`=0,`rpts`=0,`total_rpts`=0 WHERE 1

----set proshops login psw and ewallet to 1 for testing only
UPDATE  `proshops_vo`.`ir` SET  `login_pass` =  '$2a$14$phLGofdlgaOncrlFzMDiQOCg2HFVrHcmxL0/4m5uptfSZMlu0DHPe',
`ewallet_pass` =  '$2a$14$phLGofdlgaOncrlFzMDiQOCg2HFVrHcmxL0/4m5uptfSZMlu0DHPe' WHERE  `ir`.`id` =1;

----roll back proshops ir passwords for login and ewallet
UPDATE  `proshops_vo`.`ir` SET  `login_pass` =  '$2a$14$PSmzBqnEoniryOdUgPajm.5wluMliiAkHe3cHlj7iADiIzs8tZfXG',
`ewallet_pass` =  '$2a$14$YOMKSbSeCriTzKrglFRSVervuSCTWA2KfscL0RXt6.SnZk0UPkRZ.' WHERE  `ir`.`id` =1;




