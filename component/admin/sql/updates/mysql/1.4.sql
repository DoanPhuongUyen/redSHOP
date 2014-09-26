ALTER TABLE `#__redshop_quotation`
	ADD `quotation_customer_note` TEXT NOT NULL
	AFTER `quotation_note`;
ALTER TABLE `#__redshop_product`
	ADD `allow_decimal_piece` int(4) NOT NULL;
ALTER TABLE `#__redshop_country`
	DROP INDEX `idx_country_name`;
ALTER TABLE `#__redshop_currency`
	DROP INDEX `idx_currency_name`;
ALTER TABLE `#__redshop_product`
	DROP INDEX `product_number`;
DROP TABLE IF EXISTS `#__redshop_payment_method`;
