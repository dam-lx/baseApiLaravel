-- MySQL dump 10.13  Distrib 5.7.23, for Win64 (x86_64)
--
-- Host: localhost    Database: laravel_sample_2
-- ------------------------------------------------------
-- Server version	5.7.23-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


--
-- Temporary table structure for view `view_category_item_level`
--

DROP TABLE IF EXISTS `view_category_item_level`;
/*!50001 DROP VIEW IF EXISTS `view_category_item_level`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `view_category_item_level` AS SELECT
 1 AS `id`,
 1 AS `name`,
 1 AS `lft`,
 1 AS `rgt`,
 1 AS `url`,
 1 AS `order_value`,
 1 AS `level_value`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_debug_user_role_list_users`
--

DROP TABLE IF EXISTS `view_debug_user_role_list_users`;
/*!50001 DROP VIEW IF EXISTS `view_debug_user_role_list_users`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `view_debug_user_role_list_users` AS SELECT
 1 AS `user_id`,
 1 AS `user_name`,
 1 AS `user_email`,
 1 AS `user_role_value`,
 1 AS `user_active`,
 1 AS `role_id`,
 1 AS `role_name`,
 1 AS `role_value`,
 1 AS `role_description`,
 1 AS `user_birth_date`,
 1 AS `user_gender`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_get_translation_data`
--

DROP TABLE IF EXISTS `view_get_translation_data`;
/*!50001 DROP VIEW IF EXISTS `view_get_translation_data`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `view_get_translation_data` AS SELECT
 1 AS `id`,
 1 AS `lang_code`,
 1 AS `text`,
 1 AS `translate_type_code`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_roles_map_action`
--

DROP TABLE IF EXISTS `view_roles_map_action`;
/*!50001 DROP VIEW IF EXISTS `view_roles_map_action`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `view_roles_map_action` AS SELECT
 1 AS `role_id`,
 1 AS `role_map_id`,
 1 AS `role_name`,
 1 AS `role_value`,
 1 AS `role_description`,
 1 AS `module`,
 1 AS `controller`,
 1 AS `action`,
 1 AS `is_active`,
 1 AS `screen_code`*/;
SET character_set_client = @saved_cs_client;

--
-- Dumping routines for database 'laravel_sample_2'
--
/*!50003 DROP FUNCTION IF EXISTS `get_error_code` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;

CREATE  FUNCTION `get_error_code`() RETURNS int(11)
BEGIN



	RETURN -1;



END ;

/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `get_error_message` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;

CREATE  FUNCTION `get_error_message`(code INT, message_code VARCHAR(255)) RETURNS varchar(255) CHARSET utf8
BEGIN



	RETURN '';



END ;

/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `get_success_code` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;

CREATE  FUNCTION `get_success_code`() RETURNS int(11)
BEGIN



	RETURN 0;



END ;

/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `SPLIT_STRING` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;

CREATE  FUNCTION `SPLIT_STRING`(



	str LONGTEXT CHARSET utf8,



	delim VARCHAR(10) ,



	pos INT



) RETURNS longtext CHARSET utf8
RETURN REPLACE(



	SUBSTRING(



		SUBSTRING_INDEX(str , delim , pos) ,



		CHAR_LENGTH(



			SUBSTRING_INDEX(str , delim , pos - 1)



		) + 1



	) ,



	delim ,



	''



) ;


/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `DEBUG_IMPORT_AND_MERGER_ROLE_ACT` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION' */ ;

CREATE  PROCEDURE `DEBUG_IMPORT_AND_MERGER_ROLE_ACT`(p_listScreen JSON)
BEGIN
DECLARE i,j INT DEFAULT 1;



		DECLARE countElement INT DEFAULT 0;



		DECLARE screen_json_arr JSON;



		DECLARE tmpRecord JSON;



		CREATE TEMPORARY TABLE IF NOT EXISTS SCREEN_INFO (



			id INT NOT NULL  AUTO_INCREMENT,



			module VARCHAR(50) NOT NULL,



			controller VARCHAR(50) NOT NULL,



			action_name VARCHAR(50) NOT NULL,



            screen_code VARCHAR(100) NOT NULL,



            description VARCHAR(100),



			PRIMARY KEY (id)



		);



		CREATE TEMPORARY TABLE IF NOT EXISTS SCREEN_MAP_ROLE (



			module VARCHAR(50) NOT NULL,



			controller VARCHAR(50) NOT NULL,



			action_name VARCHAR(50) NOT NULL,



			role_value INT,



			is_active TINYINT(4)



		);



        CREATE TEMPORARY TABLE IF NOT EXISTS SCREEN_MAP_ROLE_INSERT (



			module VARCHAR(50) NOT NULL,



			controller VARCHAR(50) NOT NULL,



			action_name VARCHAR(50) NOT NULL,



			role_value INT,



			is_active TINYINT(4)



		);



        START TRANSACTION;



		/**



		INIT INPUT DATA



		**/



		SET screen_json_arr = JSON_EXTRACT(p_listScreen,'$.*');



		SET countElement = JSON_LENGTH(screen_json_arr) ;



		WHILE i <= countElement DO



			SET j = i-1;



			SET tmpRecord = JSON_EXTRACT(screen_json_arr,CONCAT('$[',j,']'));



			INSERT INTO SCREEN_INFO(



				id



			,	module



			,	controller



			,	action_name



            ,	screen_code



            ,	description



			)



			SELECT



				i



			,	JSON_UNQUOTE(JSON_EXTRACT(tmpRecord,'$.module'))



			,	JSON_UNQUOTE(JSON_EXTRACT(tmpRecord,'$.controller'))



			,	JSON_UNQUOTE(JSON_EXTRACT(tmpRecord,'$.action'))



            ,	JSON_UNQUOTE(JSON_EXTRACT(tmpRecord,'$.screen_code'))



            ,	JSON_UNQUOTE(JSON_EXTRACT(tmpRecord,'$.description'))



			;



			SET i = i+1;



		END WHILE;







		/** BUSSINESS**/



		INSERT SCREEN_MAP_ROLE(



			module ,



			controller,



			action_name,



			role_value,



			is_active



		)



		SELECT



			S.module



		,	S.controller



		,	S.action



		,	RMS.role_value



		,	RMS.is_active



		FROM sys_role_map_screen AS RMS



		INNER JOIN sys_screens AS S ON



			RMS.screen_id = S.id;



		/** Remove all not exists in screen list**/







		DELETE FROM SCREEN_MAP_ROLE



        WHERE



			NOT exists  (SELECT *FROM SCREEN_INFO
						WHERE SCREEN_INFO.module = SCREEN_MAP_ROLE.module
							AND SCREEN_MAP_ROLE.controller = SCREEN_INFO.controller
                            AND SCREEN_MAP_ROLE.action_name = SCREEN_INFO.action_name);












        INSERT INTO SCREEN_MAP_ROLE_INSERT(



			module ,



			controller,



			action_name,



			role_value,



			is_active



        )



        SELECT
			distinct


			module ,



			controller,



			action_name,



			role_value,



			is_active



		FROM SCREEN_MAP_ROLE;











		INSERT INTO SCREEN_MAP_ROLE_INSERT(



			module ,



			controller,



			action_name,



			role_value,



			is_active



		)



		SELECT DISTINCT



			S.module



		,	S.controller



		,	S.action_name



		,	R.role_value



		,	0



		FROM SCREEN_INFO AS S



        LEFT JOIN SCREEN_MAP_ROLE AS RMS  ON



				S.module =  RMS.module



			AND	S.controller =  RMS.controller



			AND S.action_name = RMS.action_name



        CROSS JOIN sys_roles AS R



        WHERE RMS.module IS NULL;







		TRUNCATE TABLE sys_screens;



		INSERT INTO sys_screens(



			id



		,	module



		,	controller



		,	action



        ,	screen_code



		,	description



		)



		SELECT



			id



		,	module



		,	controller



		,	action_name



        ,	screen_code



		,	description



		FROM SCREEN_INFO;







		TRUNCATE TABLE sys_role_map_screen;



		INSERT INTO sys_role_map_screen(



			role_value



		,	screen_id



		,	is_active



		)



		SELECT



			RMS.role_value



		,	S.id



		,	RMS.is_active



		FROM SCREEN_MAP_ROLE_INSERT AS RMS



		INNER JOIN sys_screens AS S ON



				RMS.module =  S.module



			AND RMS.controller = S.controller



			AND RMS.action_name = S.action;







        COMMIT;







		DROP TABLE SCREEN_INFO;



		DROP TABLE SCREEN_MAP_ROLE;



        DROP TABLE SCREEN_MAP_ROLE_INSERT;

END ;

/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sys_show_message_error` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION' */ ;

CREATE  PROCEDURE `sys_show_message_error`(code INT, dataError JSON)
BEGIN



	SELECT code AS code, dataError AS data_error;



END ;

/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sys_show_message_exception` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;

CREATE  PROCEDURE `sys_show_message_exception`(message_code varchar(500))
BEGIN



	SELECT -9999 AS code, message_code AS message_code;



END ;

/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sys_show_message_success` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;

CREATE  PROCEDURE `sys_show_message_success`()
BEGIN



	SELECT 0 AS code, 'success_message' AS message_code;



END ;

/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sys_show_result` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION' */ ;

CREATE  PROCEDURE `sys_show_result`(code INT, dataError JSON)
BEGIN



	SELECT code AS code, dataError AS data;



END ;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_get_translation_data`
--

/*!50001 DROP VIEW IF EXISTS `view_get_translation_data`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013  SQL SECURITY DEFINER */
/*!50001 VIEW `view_get_translation_data` AS select `v`.`id` AS `id`,`v`.`lang_code` AS `lang_code`,`v`.`text` AS `text`,`tt`.`code` AS `translate_type_code` from (`sys_translation` `v` left join `sys_translate_type` `tt` on((`v`.`translate_type` = `tt`.`code`))) where ((isnull(`v`.`is_deleted`) or (`v`.`is_deleted` <> 1)) and (`tt`.`code` is not null)) order by `v`.`lang_code` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_roles_map_action`
--

/*!50001 DROP VIEW IF EXISTS `view_roles_map_action`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013  SQL SECURITY DEFINER */
/*!50001 VIEW `view_roles_map_action` AS select `sr`.`id` AS `role_id`,`rms`.`id` AS `role_map_id`,`sr`.`name` AS `role_name`,`sr`.`role_value` AS `role_value`,`sr`.`description` AS `role_description`,`ss`.`module` AS `module`,`ss`.`controller` AS `controller`,`ss`.`action` AS `action`,`rms`.`is_active` AS `is_active`,`ss`.`screen_code` AS `screen_code` from (((`sys_roles` `sr` left join `sys_role_map_screen` `rms` on((`sr`.`role_value` = (`rms`.`role_value`)))) left join `sys_screens` `ss` on((`rms`.`screen_id` = (`ss`.`id` )))) join `sys_modules` `dm` on(((`dm`.`module_code` = (`ss`.`module` )) and (`dm`.`is_skip_acl` <> 1)))) order by `sr`.`role_value`,`rms`.`screen_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-11-07 10:22:44
