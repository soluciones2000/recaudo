-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-03-2018 a las 03:51:51
-- Versión del servidor: 10.1.30-MariaDB
-- Versión de PHP: 7.2.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `recaudo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `idCliente` bigint(20) NOT NULL,
  `NombreCliente` varchar(200) COLLATE utf8_bin NOT NULL,
  `IdFiscal` varchar(50) COLLATE utf8_bin NOT NULL,
  `FechaRegistro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentagestor`
--

CREATE TABLE `cuentagestor` (
  `idCuentaGestor` bigint(20) NOT NULL,
  `idGestor` bigint(20) NOT NULL,
  `idCliente` bigint(20) NOT NULL,
  `SaldoCuenta` decimal(15,2) NOT NULL,
  `FechaRegistro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentaproveedor`
--

CREATE TABLE `cuentaproveedor` (
  `idCuentaProveedor` bigint(20) NOT NULL,
  `idProveedor` bigint(20) NOT NULL,
  `idCliente` bigint(20) NOT NULL,
  `SaldoCuenta` decimal(15,2) NOT NULL,
  `FechaRegistro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalleorden`
--

CREATE TABLE `detalleorden` (
  `idDetalleOrden` bigint(20) NOT NULL,
  `idProducto` bigint(20) NOT NULL,
  `idOrden` bigint(20) NOT NULL,
  `Cantidad` int(10) NOT NULL,
  `FechaRegistro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formapago`
--

CREATE TABLE `formapago` (
  `idFormaPago` bigint(20) NOT NULL,
  `idProveedor` bigint(20) NOT NULL,
  `idGestor` bigint(20) NOT NULL,
  `NombreFormaPago` varchar(200) COLLATE utf8_bin NOT NULL,
  `FechaRegistro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formapagocliente`
--

CREATE TABLE `formapagocliente` (
  `idFormaPagoCliente` bigint(20) NOT NULL,
  `idFormaPago` bigint(20) NOT NULL,
  `idCliente` bigint(20) NOT NULL,
  `FechaRegistro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gestor`
--

CREATE TABLE `gestor` (
  `idGestor` bigint(20) NOT NULL,
  `NombreGestor` varchar(200) COLLATE utf8_bin NOT NULL,
  `IdFiscal` varchar(50) COLLATE utf8_bin NOT NULL,
  `FechaRegistro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log`
--

CREATE TABLE `log` (
  `id` int(11) NOT NULL,
  `id_users_registros` int(11) NOT NULL,
  `accion_registros` int(11) NOT NULL,
  `fecha_registros` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `log`
--

INSERT INTO `log` (`id`, `id_users_registros`, `accion_registros`, `fecha_registros`) VALUES
(1, 0, 0, 26),
(2, 0, 0, 28),
(3, 0, 0, 28),
(4, 0, 0, 28),
(5, 0, 0, 11),
(6, 0, 0, 11),
(7, 0, 0, 11),
(8, 0, 0, 23),
(9, 0, 0, 23),
(10, 0, 0, 25),
(11, 0, 0, 25),
(12, 0, 0, 25),
(13, 0, 0, 25),
(14, 0, 0, 26),
(15, 0, 0, 26),
(16, 0, 0, 26),
(17, 0, 0, 26),
(18, 0, 0, 26),
(19, 0, 0, 26),
(20, 0, 0, 26),
(21, 0, 0, 26),
(22, 0, 0, 26);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden`
--

CREATE TABLE `orden` (
  `idOrden` bigint(20) NOT NULL,
  `idCuentaProveedor` bigint(20) NOT NULL,
  `StatusOrden` varchar(20) COLLATE utf8_bin NOT NULL,
  `idTransaccion` decimal(15,2) NOT NULL,
  `FechaRegistro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `idProducto` bigint(20) NOT NULL,
  `NombreProducto` varchar(200) COLLATE utf8_bin NOT NULL,
  `FechaRegistro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productoproveedor`
--

CREATE TABLE `productoproveedor` (
  `idProductoProveedor` bigint(20) NOT NULL,
  `idProducto` bigint(20) NOT NULL,
  `idProveedor` bigint(20) NOT NULL,
  `FechaRegistro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `idProveedor` bigint(20) NOT NULL,
  `NombreProveedor` varchar(200) COLLATE utf8_bin NOT NULL,
  `IdFiscal` varchar(50) COLLATE utf8_bin NOT NULL,
  `FechaRegistro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipotransaccion`
--

CREATE TABLE `tipotransaccion` (
  `idTipoTransaccion` bigint(20) NOT NULL,
  `NombreTipoTransaccion` varchar(200) COLLATE utf8_bin NOT NULL,
  `signoTipoTransaccion` char(1) COLLATE utf8_bin NOT NULL,
  `FechaRegistro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transaccion`
--

CREATE TABLE `transaccion` (
  `idTransaccion` bigint(20) NOT NULL,
  `idFormaPagoCliente` bigint(20) NOT NULL,
  `idTipoTransaccion` bigint(20) NOT NULL,
  `idCuentaProveedor` bigint(20) NOT NULL,
  `idCuentaGestor` bigint(20) NOT NULL,
  `FechaRegistro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombres` varchar(50) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `create_on` varchar(20) NOT NULL,
  `ultimo_inicio` varchar(20) DEFAULT NULL,
  `activo` int(2) NOT NULL,
  `modulos` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombres`, `apellidos`, `correo`, `clave`, `telefono`, `create_on`, `ultimo_inicio`, `activo`, `modulos`) VALUES
(1, 'qwe', 'asd', 'qwe@qwe.com', '$2y$10$bVpPSEV9OepZqVEM8AMp7.SRlWpQc5r9BhJAj6qs9E/2mYu9XE5fO', '112345', '12-01-2018 15:41:58', '26-03-2018 03:49:12', 1, 'Empresas|1,Gestor|1,Polizas|1,Proveedor|1,Usuarios|1');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`idCliente`);

--
-- Indices de la tabla `cuentagestor`
--
ALTER TABLE `cuentagestor`
  ADD PRIMARY KEY (`idCuentaGestor`);

--
-- Indices de la tabla `cuentaproveedor`
--
ALTER TABLE `cuentaproveedor`
  ADD PRIMARY KEY (`idCuentaProveedor`);

--
-- Indices de la tabla `detalleorden`
--
ALTER TABLE `detalleorden`
  ADD PRIMARY KEY (`idDetalleOrden`);

--
-- Indices de la tabla `formapago`
--
ALTER TABLE `formapago`
  ADD PRIMARY KEY (`idFormaPago`);

--
-- Indices de la tabla `formapagocliente`
--
ALTER TABLE `formapagocliente`
  ADD PRIMARY KEY (`idFormaPagoCliente`);

--
-- Indices de la tabla `gestor`
--
ALTER TABLE `gestor`
  ADD PRIMARY KEY (`idGestor`);

--
-- Indices de la tabla `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `orden`
--
ALTER TABLE `orden`
  ADD PRIMARY KEY (`idOrden`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`idProducto`);

--
-- Indices de la tabla `productoproveedor`
--
ALTER TABLE `productoproveedor`
  ADD PRIMARY KEY (`idProductoProveedor`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`idProveedor`);

--
-- Indices de la tabla `tipotransaccion`
--
ALTER TABLE `tipotransaccion`
  ADD PRIMARY KEY (`idTipoTransaccion`);

--
-- Indices de la tabla `transaccion`
--
ALTER TABLE `transaccion`
  ADD PRIMARY KEY (`idTransaccion`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `idCliente` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cuentagestor`
--
ALTER TABLE `cuentagestor`
  MODIFY `idCuentaGestor` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cuentaproveedor`
--
ALTER TABLE `cuentaproveedor`
  MODIFY `idCuentaProveedor` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalleorden`
--
ALTER TABLE `detalleorden`
  MODIFY `idDetalleOrden` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `formapago`
--
ALTER TABLE `formapago`
  MODIFY `idFormaPago` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `formapagocliente`
--
ALTER TABLE `formapagocliente`
  MODIFY `idFormaPagoCliente` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `gestor`
--
ALTER TABLE `gestor`
  MODIFY `idGestor` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `log`
--
ALTER TABLE `log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `orden`
--
ALTER TABLE `orden`
  MODIFY `idOrden` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `idProducto` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productoproveedor`
--
ALTER TABLE `productoproveedor`
  MODIFY `idProductoProveedor` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `idProveedor` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tipotransaccion`
--
ALTER TABLE `tipotransaccion`
  MODIFY `idTipoTransaccion` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `transaccion`
--
ALTER TABLE `transaccion`
  MODIFY `idTransaccion` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
