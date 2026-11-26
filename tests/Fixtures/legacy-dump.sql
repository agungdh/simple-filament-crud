-- phpMyAdmin SQL Dump
-- Fixture used by tests/Feature/ImportLegacyDataCommandTest.php

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2025-07-13 08:55:47', '2025-07-13 08:55:47'),
(2, 'pegawai', 'web', '2025-07-13 08:55:47', '2025-07-13 08:55:47');

--
-- Table structure for table `bidangs`
--

CREATE TABLE `bidangs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bidang` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bidangs` (`id`, `bidang`, `created_at`, `updated_at`) VALUES
(1, 'BIDANG PROGRAM; PELAPORAN', '2025-07-13 08:55:47', '2025-07-13 08:55:47'),
(2, 'BIDANG O\'Brien', '2025-07-13 08:55:47', '2025-07-13 08:55:47');

--
-- Table structure for table `pangkat_golongans`
--

CREATE TABLE `pangkat_golongans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `jenjang` varchar(255) NOT NULL,
  `pangkat` varchar(255) NOT NULL,
  `golongan` varchar(255) NOT NULL,
  `ruang` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pangkat_golongans` (`id`, `jenjang`, `pangkat`, `golongan`, `ruang`, `created_at`, `updated_at`) VALUES
(1, 'Ahli Muda', 'Penata Muda', 'III', 'a', '2025-07-13 08:55:47', '2025-07-13 08:55:47');

--
-- Table structure for table `jenis_pelatihans`
--

CREATE TABLE `jenis_pelatihans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `jenis_pelatihan` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `jenis_pelatihans` (`id`, `jenis_pelatihan`, `created_at`, `updated_at`) VALUES
(1, 'Diklat Teknis', '2025-07-13 08:55:47', '2025-07-13 08:55:47');

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `username`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin1', '$2y$12$fixturefixturefixturefixturefixturefixturefixturefixturefi', NULL, '2025-07-13 08:55:48', '2025-07-13 08:55:48'),
(2, 'pegawai1', '$2y$12$fixturefixturefixturefixturefixturefixturefixturefixturefi', NULL, '2025-07-13 08:55:48', '2025-07-13 08:55:48');

--
-- Table structure for table `pegawais`
--

CREATE TABLE `pegawais` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `bidang_id` bigint(20) UNSIGNED NOT NULL,
  `pangkat_golongan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tipe` enum('pegawai','admin') NOT NULL DEFAULT 'pegawai',
  `status` enum('aktif','non aktif') NOT NULL DEFAULT 'aktif',
  `nip` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `jabatan` varchar(255) DEFAULT NULL,
  `kategori_jabatan` enum('struktural','fungsional auditor','fungsional tertentu') DEFAULT NULL,
  `peran` varchar(255) DEFAULT NULL,
  `kategori_kebutuhan_jam_pelatihan` enum('admin','pejabat','auditor') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pegawais` (`id`, `user_id`, `bidang_id`, `pangkat_golongan_id`, `tipe`, `status`, `nip`, `nama`, `jabatan`, `kategori_jabatan`, `peran`, `kategori_kebutuhan_jam_pelatihan`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'admin', 'aktif', '111', 'Admin O\'Brien', NULL, NULL, 'Sekretaris; Kaper', 'admin', '2025-07-13 08:55:48', '2025-07-13 08:55:48'),
(2, 2, 2, NULL, 'pegawai', 'non aktif', '222', 'Pegawai "Satu"', 'Staf', 'struktural', NULL, 'pejabat', '2025-07-13 08:55:48', '2025-07-13 08:55:48');

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2);

--
-- Table structure for table `ppms`
--

CREATE TABLE `ppms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pegawai_id` bigint(20) UNSIGNED NOT NULL,
  `nomor_surat` varchar(255) NOT NULL,
  `materi_pengembangan` varchar(255) NOT NULL,
  `tanggal_pelaksanaan` date NOT NULL,
  `jumlah_jam_pelatihan` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ppms` (`id`, `pegawai_id`, `nomor_surat`, `materi_pengembangan`, `tanggal_pelaksanaan`, `jumlah_jam_pelatihan`, `created_at`, `updated_at`) VALUES
(1, 1, 'ND-1', 'Materi; dengan titik koma', '2025-06-26', 3, '2025-07-13 09:48:28', '2025-07-13 09:48:28'),
(2, 2, 'ND-2', 'Materi ''kutip'' ganda;', '2025-06-27', 4, '2025-07-13 09:49:14', '2025-07-13 09:49:14');

--
-- Table structure for table `diklats`
--

CREATE TABLE `diklats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pegawai_id` bigint(20) UNSIGNED NOT NULL,
  `jenis_pelatihan_id` bigint(20) UNSIGNED NOT NULL,
  `nomor_surat` varchar(255) NOT NULL,
  `materi_pengembangan` varchar(255) NOT NULL,
  `dari_tanggal_pelaksanaan` date NOT NULL,
  `sampai_tanggal_pelaksanaan` date NOT NULL,
  `jumlah_jam_pelatihan` int(11) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `diklats` (`id`, `pegawai_id`, `jenis_pelatihan_id`, `nomor_surat`, `materi_pengembangan`, `dari_tanggal_pelaksanaan`, `sampai_tanggal_pelaksanaan`, `jumlah_jam_pelatihan`, `filename`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'ND-3', 'Diklat; Teknis', '2025-06-01', '2025-06-03', 20, NULL, '2025-07-13 09:50:00', '2025-07-13 09:50:00');

--
-- Table structure for table `materi_ppms`
--

CREATE TABLE `materi_ppms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nomor_surat` varchar(255) DEFAULT NULL,
  `nama_pemateri` varchar(255) DEFAULT NULL,
  `materi_pengembangan` varchar(255) NOT NULL,
  `tanggal_pelaksanaan` date NOT NULL,
  `link_materi` varchar(255) DEFAULT NULL,
  `link_dokumentasi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `materi_ppms` (`id`, `nomor_surat`, `nama_pemateri`, `materi_pengembangan`, `tanggal_pelaksanaan`, `link_materi`, `link_dokumentasi`, `created_at`, `updated_at`) VALUES
(1, 'ND-4', 'Pemateri O\'Neil', 'Materi PPM', '2025-06-10', NULL, NULL, '2025-07-13 09:51:00', '2025-07-13 09:51:00');

COMMIT;
