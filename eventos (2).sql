-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-02-2021 a las 05:58:44
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
            UPDATE detallefactura SET cod_cliente = cod_cliente WHERE nofactura = factura;
            
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
  `apellidos` varchar(80) NOT NULL,
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

INSERT INTO `cliente` (`idcliente`, `cedula`, `nombre`, `apellidos`, `Correo`, `telefono`, `direccion`, `dateAdd`, `usuario_id`, `status`, `cod_tarjeta`) VALUES
(75, 435435, '2323', '234', '2322@adsa.com', 986330869, 'Ecuador', '2021-02-17 23:03:41', 0, 1, '00676234'),
(76, 565123123, 'ddave', 'rodromo', 'root0166221x@gmail.com', 986330869, 'Ecuador', '2021-02-17 23:06:57', 0, 1, '9909712'),
(77, 131409834, 'lets', 'prueba', 'prueba@gail.com', 33333, '23234', '2021-02-20 23:29:04', 0, 1, ''),
(78, 2147483647, 'dave', 'Rodriguez', 'root01xr53@gmail.com', 986330869, 'Ecuador', '2021-02-20 23:32:50', 0, 1, '');

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
  `iva` decimal(10,2) NOT NULL,
  `codEvento` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `cedula`, `nombre`, `razon_social`, `telefono`, `email`, `direccion`, `iva`, `codEvento`) VALUES
(1, '000000', 'SAE', '00000000', 0, '0000@GMAIL.COM', '000000', '0.00', 12);

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
  `precio_venta` decimal(10,2) DEFAULT NULL,
  `cod_cliente` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

--
-- Volcado de datos para la tabla `detalle_temp`
--

INSERT INTO `detalle_temp` (`correlativo`, `token_user`, `codevento`, `cantidad`, `precio_venta`) VALUES
(531, 'c4ca4238a0b923820dcc509a6f75849b', 11, 1, '565.00');

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
(11, 11, '2021-02-16 12:59:22', 100, '565.00', 1),
(12, 12, '2021-02-16 15:11:44', 100, '454.00', 1),
(13, 13, '2021-02-17 23:40:38', 300, '4500.00', 1),
(14, 14, '2021-02-18 22:45:01', 100, '45.00', 1),
(15, 15, '2021-02-18 22:45:47', 45, '45.00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento`
--

CREATE TABLE `evento` (
  `codevento` int(11) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `capMax` int(11) DEFAULT NULL,
  `direccion` varchar(300) DEFAULT NULL,
  `foto` text DEFAULT NULL,
  `fecha_evento` datetime NOT NULL,
  `dateAdd` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `id_tipo_seminario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `evento`
--

INSERT INTO `evento` (`codevento`, `descripcion`, `precio`, `capMax`, `direccion`, `foto`, `fecha_evento`, `dateAdd`, `usuario_id`, `status`, `id_tipo_seminario`) VALUES
(11, 'texts1', '565.00', 83, NULL, 'img_1691018ce4e9bc9aae3630f84b912910.jpg', '2222-02-02 02:02:00', '2021-02-16 12:59:22', 1, 1, 1),
(12, 'curso de redes 3', '454.00', 87, 'utm', 'img_evento.png', '2021-02-16 15:02:00', '2021-02-16 15:11:44', 1, 1, 2),
(13, 'curso de disennio grafico 3', '4500.00', 296, 'faculta de economia56', 'img_evento.png', '2021-02-17 23:02:00', '2021-02-17 23:40:38', 1, 1, 2),
(14, 'java web', '45.00', 94, 'faculad de informatica', 'img_evento.png', '2002-05-08 00:05:00', '2021-02-18 22:45:01', 1, 1, 2),
(15, 'robotica', '45.00', 40, '34', 'img_evento.png', '2010-05-05 00:05:00', '2021-02-18 22:45:47', 1, 1, 1);

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
(260, '9909712', '2021-02-21 22:34:13'),
(261, '9909712', '2021-02-21 22:44:03'),
(262, '9909712', '2021-02-21 22:49:11'),
(263, '9909712', '2021-02-21 22:52:29'),
(264, '9909712', '2021-02-21 22:53:45'),
(265, '9909712', '2021-02-21 22:58:45'),
(266, '9909712', '2021-02-21 23:07:19'),
(267, '9909712', '2021-02-21 23:15:03'),
(268, '9909712', '2021-02-21 23:16:44'),
(269, '9909712', '2021-02-21 23:18:36'),
(270, '9909712', '2021-02-21 23:18:52'),
(271, '9909712', '2021-02-21 23:19:19'),
(272, 'font-weight: bold;', '2021-02-21 23:21:26'),
(273, '9909712', '2021-02-21 23:22:06'),
(274, '9909712', '2021-02-21 23:29:05'),
(275, '00676234', '2021-02-21 23:29:37'),
(276, '00676234', '2021-02-21 23:31:35'),
(277, '00676234', '2021-02-21 23:31:52'),
(278, '00676234', '2021-02-21 23:32:23'),
(279, '00676234', '2021-02-21 23:34:15'),
(280, '00676234', '2021-02-21 23:37:15'),
(281, 'F0F8FF', '2021-02-21 23:40:31'),
(282, 'F0F8FF', '2021-02-21 23:41:34'),
(283, 'F0F8FF', '2021-02-21 23:42:47'),
(284, '9909712', '2021-02-21 23:46:22'),
(285, '9909712', '2021-02-21 23:47:54'),
(286, '9909712', '2021-02-21 23:48:42'),
(287, '9909712', '2021-02-21 23:49:03'),
(288, '9909712	', '2021-02-21 23:49:25'),
(289, '9909712', '2021-02-21 23:50:27'),
(290, '00676234', '2021-02-21 23:50:50'),
(291, 'YY34', '2021-02-21 23:51:15'),
(292, '00676234', '2021-02-21 23:51:50'),
(293, '9909712', '2021-02-21 23:53:22'),
(294, '9909712', '2021-02-21 23:53:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prueba`
--

CREATE TABLE `prueba` (
  `id_p` int(11) NOT NULL,
  `id_tipo_semi` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
-- Estructura de tabla para la tabla `tip_seminario`
--

CREATE TABLE `tip_seminario` (
  `id_tipo_seminario` int(11) NOT NULL,
  `nombre` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tip_seminario`
--

INSERT INTO `tip_seminario` (`id_tipo_seminario`, `nombre`) VALUES
(1, 'Presencial'),
(2, 'Virtual');

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
  `Status` int(11) NOT NULL DEFAULT 1,
  `cod_cliente` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idusuario`, `nombre`, `correo`, `usuario`, `clave`, `rol`, `Status`, `cod_cliente`) VALUES
(0, 'USUARIO FINAL', 'info@final.com', 'usuarifinal', '33e78d60bc1f9dcc7291c891e6f069e4', 1, 1, 0),
(1, 'Dave', 'sql01x@gmail.com', 'admin', '202cb962ac59075b964b07152d234b70', 1, 1, NULL),
(2, 'gg', 'root01x@gmail.com', 'admin22', '81dc9bdb52d04dc20036dbd8313ed055', 2, 0, NULL),
(4, 'test01', 'test01@gmail.com', 'tes01', '202cb962ac59075b964b07152d234b70', 2, 0, NULL),
(6, 'dave', 'dave@gmail.com', 'dave001', 'fa246d0262c3925617b0c72bb20eeb1d', 2, 0, NULL),
(7, 'Kurt', 'root03@gmail.com', 'kurt23', '202cb962ac59075b964b07152d234b70', 1, 0, NULL),
(8, 'a145', 'a@gmail.com', 'a1', '202cb962ac59075b964b07152d234b70', 1, 1, NULL),
(9, 'b', 'b@gmail.com', 'b2', '202cb962ac59075b964b07152d234b70', 5, 1, NULL),
(10, 'c', 'c@gmail.com', 'cc2', '202cb962ac59075b964b07152d234b70', 2, 1, NULL),
(11, 'n', 'n@gmail.com', 'n2', '202cb962ac59075b964b07152d234b70', 5, 1, NULL),
(12, 'fr', 'fr@outllok.ccom', 'out', '202cb962ac59075b964b07152d234b70', 2, 1, NULL),
(13, 'trte', 'wee@yahoo.com', 'web', '202cb962ac59075b964b07152d234b70', 1, 1, NULL),
(14, 'kt', 'kt@gmail.com', 'kt45', '202cb962ac59075b964b07152d234b70', 1, 1, NULL),
(15, 'yu', 'yu@gmail.com', 'yu23', '202cb962ac59075b964b07152d234b70', 5, 1, NULL),
(16, '11', '11@gashd', 'admin56', '202cb962ac59075b964b07152d234b70', 2, 1, NULL),
(20, 'admin45', 'root01x@gmail.c666', '4577777', 'dcddb75469b4b4875094e14561e573d8', 5, 1, NULL),
(21, '5666666', 'root01x@wail.com', '45', '735b90b4568125ed6c3f678819b6e058', 5, 1, NULL),
(36, 'daev.ad fad', 'asdas@sdsad', 'aaddf', '4124bc0a9335c27f086f24ba207a4912', 5, 1, 0),
(38, '23423.123123', 'root1231ww01x@gmail.com', 'admin12', '202cb962ac59075b964b07152d234b70', 5, 1, 0),
(39, '2323.234', '2322@adsa.com', '6566', 'adc2985779b620ec206f3648267ca4b4', 5, 1, 0),
(40, 'ddave.rodromo', 'root0166221x@gmail.com', 'rootmain', '202cb962ac59075b964b07152d234b70', 5, 1, 0),
(41, 'lets..prueba', 'prueba@gail.com', 'prueba2000', '202cb962ac59075b964b07152d234b70', 5, 1, 0),
(42, 'dave Rodriguez', 'root01xr53@gmail.com', '4www', '310dcbbf4cce62f762a2aaa148d556bd', 5, 1, 0);

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
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `id_tipo_seminario` (`id_tipo_seminario`) USING BTREE;

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
-- Indices de la tabla `prueba`
--
ALTER TABLE `prueba`
  ADD PRIMARY KEY (`id_p`),
  ADD KEY `id_tipo_semi` (`id_tipo_semi`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`idrol`);

--
-- Indices de la tabla `tip_seminario`
--
ALTER TABLE `tip_seminario`
  ADD PRIMARY KEY (`id_tipo_seminario`);

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
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

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
  MODIFY `correlativo` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=540;

--
-- AUTO_INCREMENT de la tabla `entradas`
--
ALTER TABLE `entradas`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `evento`
--
ALTER TABLE `evento`
  MODIFY `codevento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `nofactura` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=295;

--
-- AUTO_INCREMENT de la tabla `prueba`
--
ALTER TABLE `prueba`
  MODIFY `id_p` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `idrol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tip_seminario`
--
ALTER TABLE `tip_seminario`
  MODIFY `id_tipo_seminario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

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
  ADD CONSTRAINT `evento_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `evento_ibfk_3` FOREIGN KEY (`id_tipo_seminario`) REFERENCES `tip_seminario` (`id_tipo_seminario`);

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
