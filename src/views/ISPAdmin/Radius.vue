<template>
  <v-container fluid class="pa-6">
    <div class="d-flex align-center mb-6">
      <h4 class="text-h4 font-weight-bold mb-0">
        <span class="text-medium-emphasis font-weight-light">Infrastruktur /</span> Radius Server
      </h4>
      <v-spacer></v-spacer>
      <v-btn color="primary">
        <v-icon start>bx bx-plus</v-icon> Tambah User Radius
      </v-btn>
    </div>

    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title class="pa-4 d-flex align-center">Daftar Akun Radius (PPPoE/Hotspot)</v-card-title>
          <v-divider></v-divider>
          <v-table hover>
            <thead>
              <tr>
                <th class="text-left font-weight-bold">Username</th>
                <th class="text-left font-weight-bold">Service</th>
                <th class="text-left font-weight-bold">Profile</th>
                <th class="text-left font-weight-bold">IP Address</th>
                <th class="text-left font-weight-bold">Uptime</th>
                <th class="text-left font-weight-bold" width="100">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in radiusUsers" :key="user.username">
                <td class="font-weight-medium text-primary">{{ user.username }}</td>
                <td><v-chip size="x-small" label class="text-capitalize">{{ user.service }}</v-chip></td>
                <td>{{ user.profile }}</td>
                <td>{{ user.ip }}</td>
                <td>{{ user.uptime }}</td>
                <td>
                  <v-btn icon size="x-small" variant="text"><v-icon color="secondary">bx bx-dots-vertical-rounded</v-icon></v-btn>
                </td>
              </tr>
            </tbody>
          </v-table>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';

const radiusUsers = ref([
  { username: 'pppoe_user1', service: 'pppoe', profile: 'Starter-10M', ip: '10.0.0.10', uptime: '12h 45m' },
  { username: 'hs_ani88', service: 'hotspot', profile: 'Trial-1H', ip: '10.5.0.124', uptime: '45m' },
  { username: 'pppoe_budi99', service: 'pppoe', profile: 'Premium-30M', ip: '10.0.0.15', uptime: '3d 14h' },
]);
</script>
