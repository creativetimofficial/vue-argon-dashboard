<script setup>
import VerticalNavLink from '@layouts/components/VerticalNavLink.vue'
import VerticalNavSectionTitle from '@layouts/components/VerticalNavSectionTitle.vue'
</script>

<template>
  <VerticalNavLink
    :item="{
      title: 'Dashboard',
      icon: 'bx-home-smile',
      to: '/isp-admin/dashboard',
    }"
  />

  <VerticalNavSectionTitle
    :item="{
      heading: 'Manajemen Core',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Pelanggan',
      icon: 'bx-user',
      to: '/isp-admin/customers',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Peta Pelanggan',
      icon: 'bx-map-alt',
      to: '/isp-admin/maps',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Paket Internet',
      icon: 'bx-package',
      to: '/isp-admin/packages',
    }"
  />

  <VerticalNavSectionTitle
    :item="{
      heading: 'Operasional',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Monitoring Realtime',
      icon: 'bx-pulse',
      to: '/isp-admin/monitoring',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Invoice & Tagihan',
      icon: 'bx-file',
      to: '/isp-admin/invoices',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Tiket Support',
      icon: 'bx-support',
      to: '/isp-admin/tickets',
    }"
  />

  <VerticalNavSectionTitle
    :item="{
      heading: 'Infrastruktur',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Mikrotik Management',
      icon: 'bx-broadcast',
      to: '/isp-admin/mikrotik',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Radius Server',
      icon: 'bx-server',
      to: '/isp-admin/radius',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'ACS (TR-069)',
      icon: 'bx-chip',
      to: '/isp-admin/acs',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Remote ONT Access',
      icon: 'bx-link-external',
      to: '/isp-admin/remote',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'VPN API',
      icon: 'bx-terminal',
      to: '/isp-admin/vpn-api',
    }"
  />

  <VerticalNavSectionTitle
    :item="{
      heading: 'Pemasaran & Aplikasi',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Laporan',
      icon: 'bx-bar-chart-alt-2',
      to: '/isp-admin/reports',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Portal Pelanggan',
      icon: 'bx-desktop',
      to: '/isp-admin/portal',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Android App',
      icon: 'bx-mobile-vibration',
      to: '/isp-admin/android',
    }"
  />

  <VerticalNavSectionTitle
    :item="{
      heading: 'Pengaturan Sistem',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Pengaturan',
      icon: 'bx-cog',
      to: '/isp-admin/settings',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Manajemen User',
      icon: 'bx-user-circle',
      to: '/isp-admin/users',
    }"
  />
</template>
