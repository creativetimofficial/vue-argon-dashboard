<template>
  <v-container fluid class="pa-6">
    <div class="d-flex align-center mb-6">
      <h4 class="text-h4 font-weight-bold mb-0">
        <span class="text-medium-emphasis font-weight-light">Infrastruktur /</span> Remote ONT / Forwarding
      </h4>
    </div>

    <v-row>
      <v-col cols="12">
        <v-card title="Remote Management ONT">
          <v-card-text>
             <p class="text-medium-emphasis mb-6">Kelola akses remote untuk ONT pelanggan melalui Dynamic Port Forwarding.</p>
             
             <v-table hover>
               <thead>
                 <tr>
                   <th>Nama Pelanggan</th>
                   <th>IP Local</th>
                   <th>Port Forward</th>
                   <th>Public Access</th>
                   <th>Status</th>
                   <th>Aksi</th>
                 </tr>
               </thead>
               <tbody>
                 <tr>
                   <td class="font-weight-bold">Niko G.</td>
                   <td>192.168.1.1</td>
                   <td>80 → 8001</td>
                   <td><v-btn variant="text" size="small" color="primary">http://rem.isp.id:8001</v-btn></td>
                   <td><v-chip size="x-small" color="success">Aktif</v-chip></td>
                   <td><v-btn icon size="x-small" variant="text"><v-icon>bx bx-power-off</v-icon></v-btn></td>
                 </tr>
               </tbody>
             </v-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
