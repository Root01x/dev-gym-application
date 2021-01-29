-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-01-2021 a las 21:27:35
-- Versión del servidor: 10.4.14-MariaDB
-- Versión de PHP: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `eventos`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_detalle_temp` (`codigo` INT, `cantidad` INT, `token_user` VARCHAR(50))  BEGIN
    
    	DECLARE precio_actual decimal(10,2);
        SELECT precio INTO precio_actual FROM evento WHERE codevento = codigo;
        
        INSERT INTO detalle_temp(token_user,codevento,cantidad,precio_venta) VALUES(token_user,codigo,cantidad,precio_actual);
        
        SELECT tmp.correlativo, tmp.codevento,p.descripcion,tmp.cantidad,tmp.precio_venta FROM detalle_temp tmp
        INNER JOIN evento p 
        ON tmp.codevento = p.codevento
        WHERE tmp.token_user = token_user;
        
     END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `anular_factura` (IN `no_factura` INT)  BEGIN
    	DECLARE existe_factura int;
        DECLARE registros int;
        DECLARE a int;
        
        DECLARE cod_evento int;
        DECLARE cant_evento int;
        DECLARE existencia_actual int;
        DECLARE nueva_existencia int;
        
        SET existe_factura =(SELECT COUNT(*) FROM factura WHERE nofactura = no_factura AND status = 1);
        
        IF existe_factura > 0 THEN
        	CREATE TEMPORARY TABLE tbl_tmp (
            	id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                cod_even BIGINT,
                cant_even int);
                
                SET a= 1;
                SET registros = (SELECT COUNT(*) FROM detallefactura WHERE nofactura = no_factura);
                
                IF registros > 0 THEN
                
                	INSERT INTO tbl_tmp(cod_even,cant_even) SELECT codevento,cantidad FROM detallefactura WHERE nofactura = no_factura;
                    
                    WHILE a<= registros DO	
                    	SELECT cod_even,cant_even INTO cod_evento,cant_evento FROM tbl_tmp WHERE id=a;
                        SELECT capMax INTO existencia_actual FROM evento WHERE codevento=cod_evento;
                        SET nueva_existencia = existencia_actual + cant_evento;
                        UPDATE evento SET capMax = nueva_existencia WHERE codevento = cod_evento;
                        SET a=a+1;
                    END WHILE;
                    UPDATE factura SET status = 2 WHERE nofactura = no_factura;
                    DROP TABLE tbl_tmp;
                    SELECT * FROM factura WHERE nofactura = no_factura;
                
                END IF;
            
        
        ELSE
        	SELECT 0 factura;
        END IF;
        
   END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `del_detalle_temp` (`id_detalle` INT, `token` VARCHAR(50))  BEGIN
    	DELETE FROM detalle_temp WHERE correlativo = id_detalle;
        
        SELECT tmp.correlativo, tmp.codevento, p.descripcion, tmp.cantidad, tmp.precio_venta FROM detalle_temp tmp
        INNER JOIN evento p 
        ON tmp.codevento = p.codevento
        WHERE tmp.token_user = token;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `procesar_transaccion` (IN `cod_usuario` INT, IN `cod_cliente` INT, IN `token` VARCHAR(50))  BEGIN
    	DECLARE factura INT;
        DECLARE registros INT;
        DECLARE total DECIMAL(10,2);
        
        DECLARE nueva_existencia int;
        DECLARE existencia_actual int;
        
        DECLARE tmp_cod_evento int;
        DECLARE tmp_cant_evento int;
        
        DECLARE a int;
        set a = 1;
        
        CREATE TEMPORARY TABLE tbl_tmp_tokenuser(
        		id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            	cod_even BIGINT,
            	cant_even int);
        SET registros = (SELECT COUNT(*) FROM detalle_temp WHERE token_user = token);
        
        IF registros > 0 THEN
        	INSERT INTO tbl_tmp_tokenuser(cod_even, cant_even) SELECT codevento,cantidad FROM detalle_temp WHERE token_user = token;
            INSERT INTO factura(usuario,codcliente) VALUES(cod_usuario,cod_cliente);
            SET factura = LAST_INSERT_ID();
            INSERT INTO detallefactura(nofactura,codevento,cantidad,precio_venta) SELECT (factura) as nofactura, codevento, cantidad, precio_venta FROM detalle_temp WHERE token_user = token;
            
            WHILE a<=registros DO
            	SELECT cod_even, cant_even INTO tmp_cod_evento, tmp_cant_evento FROM tbl_tmp_tokenuser WHERE id = a;
                SELECT capMax INTO existencia_actual FROM evento WHERE codevento = tmp_cod_evento;
                
                set nueva_existencia = existencia_actual - tmp_cant_evento;
                UPDATE evento SET capMax = nueva_existencia WHERE codevento = tmp_cod_evento;
                
                SET a=a+1;
            END WHILE;
            
            SET total = (SELECT SUM(cantidad * precio_venta) FROM detalle_temp WHERE token_user = token);
            UPDATE factura SET totaltFactura = total WHERE nofactura = factura;
            DELETE FROM detalle_temp WHERE token_user = token;
            TRUNCATE TABLE tbl_tmp_tokenuser;
            SELECT * FROM factura WHERE nofactura = factura;
        ELSE
        	SELECT 0;
        END IF;
    END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `idcliente` int(11) NOT NULL,
  `cedula` int(11) DEFAULT NULL,
  `nombre` varchar(80) DEFAULT NULL,
  `Correo` varchar(80) DEFAULT NULL,
  `telefono` int(11) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `dateAdd` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `cod_tarjeta` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`idcliente`, `cedula`, `nombre`, `Correo`, `telefono`, `direccion`, `dateAdd`, `usuario_id`, `status`, `cod_tarjeta`) VALUES
(1, 1315076362, 'Dave R.', 'root01x@gmail.com', 986330869, 'Ecuador Latacunga', '2021-01-14 00:00:00', 1, 1, ''),
(2, 1212121212, 'Julio Arosemena Diaz', 'goot0ss1x@gmail.com', 2147483647, 'Quitumbe', '2021-01-14 18:19:59', 1, 0, ''),
(3, 999888, 'Hector Buen Dia', 'hhhhhx@gmail.com', 111111, 'Barrio Marrueccos', '2021-01-14 18:22:09', 9, 1, ''),
(4, 99999997, 'Freed GIll', 'tree01@outlook.com', 98765444, 'Venezuela y Chimborazo', '2021-01-15 18:07:19', 1, 1, '71123'),
(5, 1315076361, 'testet', '45234as@gmail.com', 99977766, 'Ecuador los rios', '2021-01-16 00:33:43', 1, 1, ''),
(6, 0, 'dave Rodriguez', '', 986330869, 'Ecuador', '2021-01-24 12:57:22', 1, 0, ''),
(7, 77713, 'dave Rodriguez', 'root01x@gmail.ccom', 986330869, 'Ecuador', '2021-01-24 13:05:23', 1, 1, 'as123'),
(8, 21474836, 'prueba', 'uuuuuux@gmail.ccom', 1234, 'avenida las americas quito', '2021-01-24 13:16:15', 1, 1, '4523'),
(9, 777, 'Augusto', 'uuuuuux@gmail.ccom', 3333, 'calle 2 y 3', '2021-01-24 13:32:01', 1, 0, ''),
(10, 45, '', '', 0, '', '2021-01-27 14:30:20', 1, 0, ''),
(11, 5555, '555', '552@WEW', 55, '55', '2021-01-27 14:46:44', 1, 0, ''),
(25, 4, '11', 'root01x@gmail.ccom', 11, '11', '2021-01-27 18:59:13', 1, 0, ''),
(26, 12466, '555', '123@asdd', 986330869, 'Ecuador', '2021-01-27 19:07:51', 1, 1, '00167d1'),
(27, 12, '555', '123@asdd', 986330869, 'Ecuador', '2021-01-27 19:08:22', 1, 1, '00'),
(28, 122, '23', '123@asdd', 23, '213', '2021-01-27 19:17:16', 1, 1, '55'),
(29, 0, '`12', 'uuuuuux@gmail.ccom', 986330869, 'Ecuador', '2021-01-27 20:11:51', 1, 1, ''),
(30, 0, 'asd', 'ass@asd', 986330869, 'Ecuador', '2021-01-27 20:12:43', 1, 1, ''),
(31, 144444, 'arlos', '21@as', 23, '1', '2021-01-27 20:16:43', 1, 1, ''),
(32, 6, 'fff', 'root01x@gmail.ccom', 2, '1', '2021-01-27 20:28:58', 1, 1, ''),
(33, 888, '88', '88@d', 88, '88', '2021-01-27 20:32:29', 1, 0, ''),
(34, 666, '66', '66@sd', 66, '666', '2021-01-27 20:51:47', 1, 1, '444'),
(37, 77, 'ddf', 'uuuuuux@gmail.ccom', 986330869, 'Ecuador', '2021-01-27 21:12:32', 1, 0, ''),
(38, 1111, '45', 'uuuuuux@gmail.ccom', 986330869, 'Ecuador', '2021-01-27 21:21:47', 1, 1, '1234'),
(39, 56, '5', '123@asdd', 5, 'Ecuador', '2021-01-27 21:22:37', 1, 1, '556'),
(40, 22, '67', '67@GH', 89, '454', '2021-01-27 21:25:33', 1, 1, '88'),
(41, 567, '7', '552@WEW', 7, 'ASDASD', '2021-01-27 21:26:05', 1, 1, '9999'),
(42, 67, 'OO', 'root01x@gmail.ccom', 9, 'Venezuela y Chimborazo cell: 0981860428', '2021-01-27 21:28:32', 1, 1, '130110'),
(43, 5678, '6767', '6767@ddf', 6767, 'Venezuela y Chimborazo cell: 0981860428', '2021-01-28 08:06:32', 1, 1, ''),
(44, 2222, '2222', 'root01x@gmail.com', 986330869, '2222', '2021-01-28 08:17:32', 1, 1, ''),
(45, 2, 'DDD', '222@5', 22, '1', '2021-01-28 08:22:05', 1, 0, '99822555'),
(47, 66666, 'hjy', 'root01x@gmail.ccom', 5656, 'aaasd', '2021-01-28 10:08:56', 1, 0, ''),
(48, 787, 'dff', '676@df', 5657, '56', '2021-01-28 10:16:12', 1, 0, ''),
(49, 78, 'dave Rodriguez', 'ret5@sdf', 986330869, 'Ecuador', '2021-01-28 12:19:15', 1, 1, ''),
(50, 999900, '45', '45@sd', 45, '45', '2021-01-28 12:30:09', 1, 1, ''),
(51, 45, 'dave Rodriguez', '21@as', 986330869, 'Ecuador', '2021-01-28 14:08:22', 1, 1, '77852F4E'),
(52, 1314286947, 'gerardo velez', 'root01x@gmail.ccom', 0, 'jjsakdasd ', '2021-01-28 15:07:19', 1, 1, '89A1F9B9');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` bigint(20) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `razon_social` varchar(100) NOT NULL,
  `telefono` bigint(20) NOT NULL,
  `email` varchar(200) NOT NULL,
  `direccion` text NOT NULL,
  `iva` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `cedula`, `nombre`, `razon_social`, `telefono`, `email`, `direccion`, `iva`) VALUES
(1, '000000', 'SAE', '00000000', 0, '0000@GMAIL.COM', '000000', '0.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos`
--

CREATE TABLE `datos` (
  `id` int(10) NOT NULL,
  `uid` varchar(45) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `apellido` varchar(45) NOT NULL,
  `cedula` varchar(45) NOT NULL,
  `tipo_usuario` varchar(45) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `datos`
--

INSERT INTO `datos` (`id`, `uid`, `nombre`, `apellido`, `cedula`, `tipo_usuario`, `fecha`) VALUES
(1, '77852F4E', 'Juan', 'Perez', '1214502235', 'estudiante', '2021-01-24 00:39:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallefactura`
--

CREATE TABLE `detallefactura` (
  `correlativo` bigint(11) NOT NULL,
  `nofactura` bigint(11) DEFAULT NULL,
  `codevento` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_venta` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `detallefactura`
--

INSERT INTO `detallefactura` (`correlativo`, `nofactura`, `codevento`, `cantidad`, `precio_venta`) VALUES
(13, 5, 2, 1, '20.00'),
(14, 5, 5, 1, '5.00'),
(15, 5, 6, 1, '50.00'),
(16, 6, 2, 1, '20.00'),
(17, 6, 5, 1, '5.00'),
(19, 7, 2, 1, '20.00'),
(20, 8, 2, 1, '20.00'),
(21, 9, 2, 1, '20.00'),
(22, 10, 2, 1, '20.00'),
(23, 10, 2, 2, '20.00'),
(25, 11, 3, 1, '0.00'),
(26, 11, 5, 1, '5.00'),
(27, 11, 6, 1, '50.00'),
(28, 12, 2, 1, '20.00'),
(29, 12, 2, 1, '20.00'),
(31, 13, 2, 1, '20.00'),
(32, 13, 2, 1, '20.00'),
(34, 14, 2, 1, '20.00'),
(35, 15, 2, 1, '20.00'),
(36, 16, 2, 1, '20.00'),
(37, 17, 2, 1, '20.00'),
(38, 18, 3, 1, '0.00'),
(39, 18, 3, 1, '0.00'),
(40, 18, 3, 1, '0.00'),
(41, 18, 3, 1, '0.00'),
(42, 18, 3, 1, '0.00'),
(45, 19, 2, 1, '20.00'),
(46, 20, 2, 1, '20.00'),
(47, 21, 2, 1, '20.00'),
(48, 21, 3, 1, '0.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_temp`
--

CREATE TABLE `detalle_temp` (
  `correlativo` int(11) NOT NULL,
  `token_user` varchar(50) NOT NULL,
  `codevento` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entradas`
--

CREATE TABLE `entradas` (
  `correlativo` int(11) NOT NULL,
  `codevento` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `usuario_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `entradas`
--

INSERT INTO `entradas` (`correlativo`, `codevento`, `fecha`, `cantidad`, `precio`, `usuario_id`) VALUES
(1, 1, '2021-01-16 17:02:58', 30, '200.00', 1),
(2, 2, '2021-01-16 19:44:00', 40, '20.00', 1),
(3, 3, '2021-01-16 20:03:29', 100, '0.00', 1),
(4, 4, '2021-01-16 20:04:43', 100, '0.00', 1),
(5, 5, '2021-01-17 11:00:48', 50, '5.00', 1),
(6, 6, '2021-01-18 17:42:00', 40, '50.00', 1),
(7, 7, '2021-01-27 23:15:55', 1000, '9.00', 1),
(8, 8, '2021-01-27 23:17:57', 10, '34.00', 1),
(9, 9, '2021-01-27 23:22:11', 1000, '2000.00', 1),
(10, 10, '2021-01-27 23:23:41', 100, '0.00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento`
--

CREATE TABLE `evento` (
  `codevento` int(11) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `capMax` int(11) DEFAULT NULL,
  `foto` text DEFAULT NULL,
  `fecha_evento` datetime NOT NULL,
  `dateAdd` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `evento`
--

INSERT INTO `evento` (`codevento`, `descripcion`, `precio`, `capMax`, `foto`, `fecha_evento`, `dateAdd`, `usuario_id`, `status`) VALUES
(1, 'Feria UTM', '200.00', 30, 'img_evento.png', '2023-01-23 17:01:00', '2021-01-16 17:02:58', 1, 0),
(2, 'Seminario De Redes', '20.00', 13, 'img_evento.png', '2021-01-16 19:01:00', '2021-01-16 19:44:00', 1, 1),
(3, 'Curso Oracle DB', '0.00', 15, 'img_488230e6a8c6e68241e1e43ed8377d55.jpg', '2021-06-20 00:06:00', '2021-01-16 20:03:29', 1, 1),
(4, 'Curso Oracle DB', '0.00', 40, 'img_02ec9521f44cdda389ac596266b33095.jpg', '2021-01-16 20:01:00', '2021-01-16 20:04:43', 1, 0),
(5, 'Baile de Gala UTM', '5.00', 20, 'img_99b22362dd94456c679da16b6071f2d1.jpg', '2024-02-02 02:02:00', '2021-01-17 11:00:48', 1, 1),
(6, 'Curso Oracle DB 3', '50.00', 20, 'img_dcc4f5dafabc6f7b5833cf8c38b0ad6d.jpg', '2021-01-18 17:01:00', '2021-01-18 17:42:00', 1, 1),
(7, 'javascript avanzado', '9.00', 1000, 'img_6a437a2dcf69b441609855d60b635f57.jpg', '2021-01-11 23:01:00', '2021-01-27 23:15:55', 1, 1),
(8, 'curso CSS inermedio', '34.00', 10, 'img_evento.png', '2021-01-16 03:01:00', '2021-01-27 23:17:57', 1, 1),
(9, 'CURSO DE GESTION DE PROYECTOS', '2000.00', 1000, 'img_evento.png', '1999-01-27 05:01:00', '2021-01-27 23:22:11', 1, 1),
(10, 'PRUEBA DE FECHA', '0.00', 100, 'img_evento.png', '2019-02-13 02:02:00', '2021-01-27 23:23:41', 1, 1);

--
-- Disparadores `evento`
--
DELIMITER $$
CREATE TRIGGER `entradas_A_I` AFTER INSERT ON `evento` FOR EACH ROW BEGIN 
    	INSERT INTO entradas(codevento,cantidad,precio,usuario_id)
        VALUES(new.codevento,new.capMax,new.precio,new.usuario_id);
    END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `nofactura` bigint(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario` int(11) DEFAULT NULL,
  `codcliente` int(11) DEFAULT NULL,
  `totaltFactura` decimal(10,2) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `factura`
--

INSERT INTO `factura` (`nofactura`, `fecha`, `usuario`, `codcliente`, `totaltFactura`, `status`) VALUES
(5, '2021-01-26 09:38:59', 1, 3, '75.00', 2),
(6, '2021-01-26 10:26:14', 1, 3, '25.00', 2),
(7, '2021-01-26 14:44:29', 1, 1, '20.00', 1),
(8, '2021-01-26 14:45:18', 1, 3, '20.00', 1),
(9, '2021-01-26 14:46:06', 1, 3, '20.00', 2),
(10, '2021-01-26 15:14:00', 1, 3, '60.00', 2),
(11, '2021-01-26 20:50:54', 1, 7, '55.00', 2),
(12, '2021-01-27 12:07:13', 1, 7, '40.00', 1),
(13, '2021-01-28 08:41:05', 1, 1, '40.00', 1),
(14, '2021-01-28 12:19:46', 1, 49, '20.00', 2),
(15, '2021-01-28 12:30:24', 1, 50, '20.00', 2),
(16, '2021-01-28 13:08:20', 1, 3, '20.00', 1),
(17, '2021-01-28 13:12:18', 1, 3, '20.00', 1),
(18, '2021-01-28 13:13:13', 1, 1, '0.00', 1),
(19, '2021-01-28 13:26:56', 1, 3, '20.00', 1),
(20, '2021-01-28 14:08:51', 1, 51, '20.00', 1),
(21, '2021-01-28 15:08:39', 1, 52, '20.00', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresos`
--

CREATE TABLE `ingresos` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` varchar(45) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `ingresos`
--

INSERT INTO `ingresos` (`id`, `uid`, `fecha`) VALUES
(1, '89A1F9B9', '2021-01-24 01:08:14'),
(2, '77852F4E', '2021-01-24 01:08:17'),
(3, '77852F4E', '2021-01-24 01:11:04'),
(4, '77852F4E', '2021-01-24 01:11:44'),
(5, '89A1F9B9', '2021-01-24 01:11:48'),
(6, '77852F4E', '2021-01-24 01:11:55'),
(7, '77852F4E', '2021-01-24 01:11:58'),
(8, '77852F4E', '2021-01-24 01:12:01'),
(9, '89A1F9B9', '2021-01-24 01:12:05'),
(10, '89A1F9B9', '2021-01-24 01:12:07'),
(11, '89A1F9B9', '2021-01-24 01:12:10'),
(12, '89A1F9B9', '2021-01-24 01:12:13'),
(13, '77852F4E', '2021-01-24 00:37:02'),
(14, '89A1F9B9', '2021-01-24 00:37:13'),
(15, '77852F4E', '2021-01-24 00:38:12'),
(16, '77852F4E', '2021-01-24 00:39:36'),
(17, '89A1F9B9', '2021-01-24 00:39:41'),
(18, '77852F4E', '2021-01-24 00:43:16'),
(19, '77852F4E', '2021-01-24 00:45:30'),
(20, '77852F4E', '2021-01-24 00:45:34'),
(21, '89A1F9B9', '2021-01-24 00:45:37'),
(22, '77852F4E', '2021-01-24 00:47:22'),
(23, '89A1F9B9', '2021-01-24 00:47:26'),
(24, '89A1F9B9', '2021-01-24 00:47:33'),
(25, '77852F4E', '2021-01-24 00:47:36'),
(26, '89A1F9B9', '2021-01-24 00:47:40'),
(27, '77852F4E', '2021-01-24 00:48:19'),
(28, '89A1F9B9', '2021-01-24 00:48:22'),
(29, '77852F4E', '2021-01-24 00:49:43'),
(30, '89A1F9B9', '2021-01-24 00:49:46'),
(31, '89A1F9B9', '2021-01-24 00:49:51'),
(32, '77852F4E', '2021-01-24 00:49:56'),
(33, '89A1F9B9', '2021-01-24 00:49:59'),
(34, '77852F4E', '2021-01-24 00:50:04'),
(35, '89A1F9B9', '2021-01-24 00:50:09'),
(36, '89A1F9B9', '2021-01-27 12:36:23'),
(37, '77852F4E', '2021-01-27 12:36:28'),
(38, '77852F4E', '2021-01-27 12:37:11'),
(39, '77852F4E', '2021-01-27 12:56:43'),
(40, '77852F4E', '2021-01-27 16:59:31'),
(41, '77852F4E', '2021-01-27 17:02:00'),
(42, '89A1F9B9', '2021-01-27 17:04:40'),
(43, '89A1F9B9', '2021-01-27 17:05:14'),
(44, '77852F4E', '2021-01-27 17:06:02'),
(45, '77852F4E', '2021-01-27 17:06:06'),
(46, '77852F4E', '2021-01-27 17:07:06'),
(47, '89A1F9B9', '2021-01-27 17:07:11'),
(48, '89A1F9B9', '2021-01-27 17:07:40'),
(49, '89A1F9B9', '2021-01-27 17:08:09'),
(50, '89A1F9B9', '2021-01-27 17:14:38'),
(51, '77852F4E', '2021-01-27 17:14:54'),
(52, '77852F4E', '2021-01-27 17:15:23'),
(53, '89A1F9B9', '2021-01-27 17:16:04'),
(54, '77852F4E', '2021-01-27 17:16:21'),
(65, '77852F4E', '2021-01-27 17:21:56'),
(66, '89A1F9B9', '2021-01-27 17:22:16'),
(67, '89A1F9B9', '2021-01-27 17:22:31'),
(68, '89A1F9B9', '2021-01-27 17:23:13'),
(69, '77852F4E', '2021-01-27 17:24:33'),
(70, '89A1F9B9', '2021-01-27 17:24:40'),
(71, '77852F4E', '2021-01-27 17:25:13'),
(72, '89A1F9B9', '2021-01-27 17:25:31'),
(73, '77852F4E', '2021-01-27 17:25:50'),
(74, '89A1F9B9', '2021-01-27 17:26:11'),
(75, '77852F4E', '2021-01-27 20:46:51'),
(76, '89A1F9B9', '2021-01-27 20:47:36'),
(77, '77852F4E', '2021-01-27 20:50:25'),
(78, '89A1F9B9', '2021-01-27 20:52:49'),
(79, '89A1F9B9', '2021-01-27 20:54:47'),
(80, '77852F4E', '2021-01-27 20:55:56'),
(81, '77852F4E', '2021-01-27 20:56:47'),
(82, '77852F4E', '2021-01-27 20:57:58'),
(83, '77852F4E', '2021-01-27 21:12:25'),
(84, '77852F4E', '2021-01-27 21:13:53'),
(85, '77852F4E', '2021-01-27 21:58:00'),
(86, '77852F4E', '2021-01-27 21:58:20'),
(87, '77852F4E', '2021-01-27 21:59:03'),
(88, '89A1F9B9', '2021-01-28 09:41:37'),
(89, '77852F4E', '2021-01-28 09:41:50'),
(90, '77852F4E', '2021-01-28 09:42:45'),
(91, '89A1F9B9', '2021-01-28 09:43:22'),
(92, '77852F4E', '2021-01-28 09:43:42'),
(93, '77852F4E', '2021-01-28 09:46:44'),
(94, '89A1F9B9', '2021-01-28 09:46:50'),
(95, '89A1F9B9', '2021-01-28 09:48:03'),
(96, '89A1F9B9', '2021-01-28 09:48:21'),
(97, '89A1F9B9', '2021-01-28 09:48:49'),
(98, '77852F4E', '2021-01-28 09:48:57'),
(99, '77852F4E', '2021-01-28 09:49:21'),
(100, '89A1F9B9', '2021-01-28 09:49:26'),
(101, '77852F4E', '2021-01-28 09:50:24'),
(102, '89A1F9B9', '2021-01-28 09:50:30'),
(103, '89A1F9B9', '2021-01-28 09:50:39'),
(104, '89A1F9B9', '2021-01-28 09:50:42'),
(105, '77852F4E', '2021-01-28 09:50:46'),
(106, '77852F4E', '2021-01-28 09:51:39'),
(107, '89A1F9B9', '2021-01-28 09:51:49'),
(108, '77852F4E', '2021-01-28 09:57:03'),
(109, '89A1F9B9', '2021-01-28 09:57:10'),
(110, '', '2021-01-28 09:59:07'),
(111, '', '2021-01-28 10:00:06'),
(112, '', '2021-01-28 10:00:08'),
(113, '77852F4E', '2021-01-28 10:00:12'),
(114, '89A1F9B9', '2021-01-28 10:00:26'),
(115, '77852F4E', '2021-01-28 10:02:22'),
(116, '77852F4E', '2021-01-28 10:05:38'),
(117, '89A1F9B9', '2021-01-28 10:05:44'),
(118, '89A1F9B9', '2021-01-28 10:06:05'),
(119, '77852F4E', '2021-01-28 10:06:10'),
(120, '77852F4E', '2021-01-28 10:06:56'),
(121, '89A1F9B9', '2021-01-28 10:07:00'),
(122, '77852F4E', '2021-01-28 10:07:14'),
(123, '89A1F9B9', '2021-01-28 10:07:20'),
(124, '77852F4E', '2021-01-28 10:07:44'),
(125, '89A1F9B9', '2021-01-28 10:07:56'),
(126, '89A1F9B9', '2021-01-28 10:08:51'),
(127, '89A1F9B9', '2021-01-28 10:09:02'),
(128, '77852F4E', '2021-01-28 10:11:33'),
(129, '77852F4E', '2021-01-28 10:11:57'),
(130, '77852F4E', '2021-01-28 10:16:07'),
(131, '77852F4E', '2021-01-28 10:16:43'),
(132, '', '2021-01-28 11:04:51'),
(133, '77852F4E', '2021-01-28 12:12:42'),
(134, '89A1F9B9', '2021-01-28 12:12:47'),
(135, '77852F4E', '2021-01-28 12:14:11'),
(136, '89A1F9B9', '2021-01-28 12:14:26'),
(137, '77852F4E', '2021-01-28 12:16:30'),
(138, '77852F4E', '2021-01-28 12:19:04'),
(139, '77852F4E', '2021-01-28 12:19:25'),
(140, '77852F4E', '2021-01-28 12:19:58'),
(141, '77852F4E', '2021-01-28 12:20:15'),
(142, '77852F4E', '2021-01-28 12:23:38'),
(143, '89A1F9B9', '2021-01-28 12:23:45'),
(144, '77852F4E', '2021-01-28 12:24:22'),
(145, '77852F4E', '2021-01-28 12:26:24'),
(146, '77852F4E', '2021-01-28 12:27:22'),
(147, '77852F4E', '2021-01-28 12:28:14'),
(148, '89A1F9B9', '2021-01-28 12:29:12'),
(149, '89A1F9B9', '2021-01-28 12:30:15'),
(150, '89A1F9B9', '2021-01-28 12:30:28'),
(151, '77852F4E', '2021-01-28 12:32:14'),
(152, '89A1F9B9', '2021-01-28 12:34:07'),
(153, '89A1F9B9', '2021-01-28 12:34:17'),
(154, '89A1F9B9', '2021-01-28 12:35:42'),
(155, '77852F4E', '2021-01-28 14:04:43'),
(156, '89A1F9B9', '2021-01-28 14:04:49'),
(157, '77852F4E', '2021-01-28 14:05:26'),
(158, '77852F4E', '2021-01-28 14:08:29'),
(159, '77852F4E', '2021-01-28 14:08:59'),
(160, '89A1F9B9', '2021-01-28 14:09:08'),
(161, '77852F4E', '2021-01-28 14:09:18'),
(162, '77852F4E', '2021-01-28 14:10:13'),
(163, '89A1F9B9', '2021-01-28 14:10:18'),
(164, '77852F4E', '2021-01-28 14:55:35'),
(165, '89A1F9B9', '2021-01-28 14:55:38'),
(166, '77852F4E', '2021-01-28 15:06:53'),
(167, '89A1F9B9', '2021-01-28 15:07:14'),
(168, '89A1F9B9', '2021-01-28 15:08:10'),
(169, '77852F4E', '2021-01-28 15:08:14'),
(170, '89A1F9B9', '2021-01-28 15:08:19'),
(171, '89A1F9B9', '2021-01-28 15:08:48'),
(172, '89A1F9B9', '2021-01-28 15:13:30'),
(173, '89A1F9B9', '2021-01-28 15:13:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `idrol` int(11) NOT NULL,
  `rol` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`idrol`, `rol`) VALUES
(1, 'Administrador'),
(2, 'Supervisor'),
(5, 'Asistente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idusuario` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `usuario` varchar(15) DEFAULT NULL,
  `clave` varchar(100) DEFAULT NULL,
  `rol` int(11) DEFAULT NULL,
  `Status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idusuario`, `nombre`, `correo`, `usuario`, `clave`, `rol`, `Status`) VALUES
(1, 'Dave', 'sql01x@gmail.com', 'admin', '202cb962ac59075b964b07152d234b70', 1, 1),
(2, 'gg', 'root01x@gmail.com', 'admin22', '81dc9bdb52d04dc20036dbd8313ed055', 2, 0),
(4, 'test01', 'test01@gmail.com', 'tes01', '202cb962ac59075b964b07152d234b70', 2, 0),
(6, 'dave', 'dave@gmail.com', 'dave001', 'fa246d0262c3925617b0c72bb20eeb1d', 2, 0),
(7, 'Kurt', 'root03@gmail.com', 'kurt23', '202cb962ac59075b964b07152d234b70', 1, 0),
(8, 'a145', 'a@gmail.com', 'a1', '202cb962ac59075b964b07152d234b70', 1, 1),
(9, 'b', 'b@gmail.com', 'b2', '202cb962ac59075b964b07152d234b70', 5, 1),
(10, 'c', 'c@gmail.com', 'cc2', '202cb962ac59075b964b07152d234b70', 2, 1),
(11, 'n', 'n@gmail.com', 'n2', '202cb962ac59075b964b07152d234b70', 5, 1),
(12, 'fr', 'fr@outllok.ccom', 'out', '202cb962ac59075b964b07152d234b70', 2, 1),
(13, 'trte', 'wee@yahoo.com', 'web', '202cb962ac59075b964b07152d234b70', 1, 1),
(14, 'kt', 'kt@gmail.com', 'kt45', '202cb962ac59075b964b07152d234b70', 1, 1),
(15, 'yu', 'yu@gmail.com', 'yu23', '202cb962ac59075b964b07152d234b70', 5, 1),
(16, '11', '11@gashd', 'admin56', '202cb962ac59075b964b07152d234b70', 2, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`idcliente`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `id_targeta` (`cod_tarjeta`),
  ADD KEY `id_targeta_2` (`cod_tarjeta`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `datos`
--
ALTER TABLE `datos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `codproducto` (`codevento`),
  ADD KEY `nofactura` (`nofactura`);

--
-- Indices de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `nofactura` (`token_user`),
  ADD KEY `codproducto` (`codevento`);

--
-- Indices de la tabla `entradas`
--
ALTER TABLE `entradas`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `codproducto` (`codevento`);

--
-- Indices de la tabla `evento`
--
ALTER TABLE `evento`
  ADD PRIMARY KEY (`codevento`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`nofactura`),
  ADD KEY `usuario` (`usuario`),
  ADD KEY `codcliente` (`codcliente`);

--
-- Indices de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`idrol`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idusuario`),
  ADD KEY `rol` (`rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `datos`
--
ALTER TABLE `datos`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  MODIFY `correlativo` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT de la tabla `entradas`
--
ALTER TABLE `entradas`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `evento`
--
ALTER TABLE `evento`
  MODIFY `codevento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `nofactura` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `idrol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`);

--
-- Filtros para la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  ADD CONSTRAINT `detallefactura_ibfk_1` FOREIGN KEY (`nofactura`) REFERENCES `factura` (`nofactura`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detallefactura_ibfk_2` FOREIGN KEY (`codevento`) REFERENCES `evento` (`codevento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD CONSTRAINT `detalle_temp_ibfk_2` FOREIGN KEY (`codevento`) REFERENCES `evento` (`codevento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `entradas`
--
ALTER TABLE `entradas`
  ADD CONSTRAINT `entradas_ibfk_1` FOREIGN KEY (`codevento`) REFERENCES `evento` (`codevento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `evento`
--
ALTER TABLE `evento`
  ADD CONSTRAINT `evento_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `factura_ibfk_1` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `factura_ibfk_2` FOREIGN KEY (`codcliente`) REFERENCES `cliente` (`idcliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`rol`) REFERENCES `rol` (`idrol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
