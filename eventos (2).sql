-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-06-2021 a las 07:13:23
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
        
        SET existe_factura =(SELECT COUNT(*) FROM factura WHERE nofactura = no_factura AND status !=2);
        
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
  `token_user` varchar(100) NOT NULL,
  `cod_tarjeta` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`idcliente`, `cedula`, `nombre`, `apellidos`, `Correo`, `telefono`, `direccion`, `dateAdd`, `usuario_id`, `status`, `token_user`, `cod_tarjeta`) VALUES
(105, 99999993, 'pedro', 'diaz', 'sss@gmail.com', 89987665, 'calle 2y 4', '2021-05-24 11:24:27', 0, 1, '', ''),
(106, 2147435, 'pedro', 'der', 'root01x444@gmail.com', 986330869, 'Ecuador', '2021-05-24 11:56:44', 0, 1, '798458950d8aa2407d9c9804097fefe2', '88888'),
(107, 2147, 'Freed GIll', 'Rodriguez', 'r22123oot01x@gmail.com', 963684950, 'Ecuador', '2021-05-24 12:14:51', 0, 1, '8ab09c67e1b32c7627eb64c69c157911', ''),
(108, 2147483647, 'dave', 'Rodriguez', 'root01x@gmail.com', 986330869, 'Ecuador', '2021-05-24 12:26:21', 0, 1, 'bb64720440379bd6fe8bebc726e81720', ''),
(109, 33333, 'dad', 'ad', 'roo22t01x@gmail.com', 99, '1221', '2021-05-24 12:33:01', 0, 1, '7a88b6e7dfcb186b867931c851f5f330', ''),
(110, 33223, 'dave', 'Rodriguez', 'root01111111x@gmail.com', 986330869, 'Ecuador', '2021-05-24 12:35:18', 0, 1, '9f45cfed1fd747389c7782c6cc9a88e6', ''),
(111, 234234, 'dave', 'Rodriguez', 'roo111sst01x@gmail.com', 986330869, 'Ecuador', '2021-05-24 12:36:30', 0, 1, '6bed66793418e630fee75d75c062472b', ''),
(112, 2147483647, 'dave', 'Rodriguez', 'root22201x@gmail.com', 986330869, 'Ecuador', '2021-05-24 12:48:59', 1, 1, '1a9efd7c8b7a184e3d79314f048590c9', '');

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
(1, '000000', 'SAS', 'Sistema de Acceso a Seminarios', 9999997, 'SAS@GMAIL.COM', 'CALLE 3 Y 5', '0.00', 12);

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
(23, 23, '2021-06-13 14:19:31', 22, '333.00', 1),
(24, 24, '2021-06-13 14:20:01', 223, '11.00', 1),
(25, 25, '2021-06-13 14:57:12', 222, '2323.00', 1),
(26, 26, '2021-06-13 14:58:31', 40, '56456.00', 1);

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
(23, 'redes', '333.00', 11, 'Ecuador l', 'img_2c31f53cf89da36c4bbbaa6d3b5af9c6.jpg', '2021-06-13 14:06:00', '2021-06-13 14:19:31', 1, 1, 1),
(24, 'javascript avanzado', '11.00', 211, 'Ecuador l', 'img_6b70ae11c3d2c284ec427489a72b11d6.jpg', '2021-06-14 14:06:00', '2021-06-13 14:20:01', 1, 1, 1),
(25, 'freee', '2323.00', 213, 'Venezuela y Chimborazo cell: 0981860428', 'C:xampp	mpphpA645.tmp', '2021-06-13 14:06:00', '2021-06-13 14:57:12', 1, 1, 1),
(26, 'testte', '56456.00', 33, 'faculta de economia56', 'C:xampp	mpphpD8BB.tmp', '2021-06-13 14:06:00', '2021-06-13 14:58:31', 1, 1, 1);

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
  `status` int(11) NOT NULL DEFAULT 1,
  `boucher` varchar(200) DEFAULT '0000000',
  `img_boucher` varchar(100) DEFAULT 'img_boucher.png'
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
(3, 'Asistente'),
(5, 'Cliente');

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
  `cod_cliente` int(11) DEFAULT 0,
  `token_user` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idusuario`, `nombre`, `correo`, `usuario`, `clave`, `rol`, `Status`, `cod_cliente`, `token_user`) VALUES
(0, 'USUARIO FINAL', 'info@final.com', 'usuarifinal', '33e78d60bc1f9dcc7291c891e6f069e4', 1, 1, 0, ''),
(1, 'Dave', 'sql01x@gmail.com', 'admin', '202cb962ac59075b964b07152d234b70', 1, 1, NULL, ''),
(69, 'pedro diaz', 'sss@gmail.com', 'cliente3', '202cb962ac59075b964b07152d234b70', 5, 1, 0, ''),
(70, 'pedro der', 'root01x444@gmail.com', 'cliente4', '202cb962ac59075b964b07152d234b70', 5, 1, 0, '798458950d8aa2407d9c9804097fefe2'),
(71, 'Freed GIll Rodriguez', 'r22123oot01x@gmail.com', 'cliente5', '202cb962ac59075b964b07152d234b70', 5, 1, 0, '8ab09c67e1b32c7627eb64c69c157911'),
(72, 'dave Rodriguez', 'root01x@gmail.com', 'cliente8', '202cb962ac59075b964b07152d234b70', 5, 1, 0, 'bb64720440379bd6fe8bebc726e81720'),
(73, 'dad ad', 'roo22t01x@gmail.com', 'freea213', '202cb962ac59075b964b07152d234b70', 5, 1, 0, '7a88b6e7dfcb186b867931c851f5f330'),
(74, 'dave Rodriguez', 'root01111111x@gmail.com', 'trrt', '202cb962ac59075b964b07152d234b70', 5, 1, 0, '9f45cfed1fd747389c7782c6cc9a88e6'),
(75, 'dave Rodriguez', 'roo111sst01x@gmail.com', 'cliente9', '202cb962ac59075b964b07152d234b70', 5, 1, 0, '6bed66793418e630fee75d75c062472b'),
(76, 'dave Rodriguez', 'root22201x@gmail.com', 'geee', '202cb962ac59075b964b07152d234b70', 5, 1, 0, '1a9efd7c8b7a184e3d79314f048590c9');

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
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

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
  MODIFY `correlativo` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=381;

--
-- AUTO_INCREMENT de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=819;

--
-- AUTO_INCREMENT de la tabla `entradas`
--
ALTER TABLE `entradas`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `evento`
--
ALTER TABLE `evento`
  MODIFY `codevento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `nofactura` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=243;

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
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

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
