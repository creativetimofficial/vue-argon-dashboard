<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import avatar1 from '@images/avatars/avatar-1.png';

const router = useRouter();

const adminUser = computed(() => {
  const str = localStorage.getItem('isp_admin_user') || sessionStorage.getItem('user');
  try { return JSON.parse(str || '{}'); } catch { return {}; }
});

const logout = () => {
  localStorage.removeItem('isp_admin_token');
  localStorage.removeItem('isp_admin_user');
  sessionStorage.removeItem('auth_token');
  sessionStorage.removeItem('user');
  router.push('/login');
};
</script>

<template>
  <VBadge
    dot
    location="bottom right"
    offset-x="3"
    offset-y="3"
    color="success"
    bordered
  >
    <VAvatar
      class="cursor-pointer"
      color="primary"
      variant="tonal"
    >
      <VImg :src="avatar1" />

      <!-- SECTION Menu -->
      <VMenu
        activator="parent"
        width="230"
        location="bottom end"
        offset="14px"
      >
        <VList>
          <!-- 👉 User Avatar & Name -->
          <VListItem>
            <template #prepend>
              <VListItemAction start>
                <VBadge
                  dot
                  location="bottom right"
                  offset-x="3"
                  offset-y="3"
                  color="success"
                >
                  <VAvatar
                    color="primary"
                    variant="tonal"
                  >
                    <VImg :src="avatar1" />
                  </VAvatar>
                </VBadge>
              </VListItemAction>
            </template>

            <VListItemTitle class="font-weight-semibold">
              {{ adminUser.name || 'Admin' }}
            </VListItemTitle>
            <VListItemSubtitle>{{ adminUser.email || 'Administrator' }}</VListItemSubtitle>
          </VListItem>
          <VDivider class="my-2" />

          <!-- 👉 Profile -->
          <VListItem :to="'/isp-admin/settings'">
            <template #prepend>
              <VIcon
                class="me-2"
                icon="bx-user"
                size="22"
              />
            </template>

            <VListItemTitle>Profil</VListItemTitle>
          </VListItem>

          <!-- Divider -->
          <VDivider class="my-2" />

          <!-- 👉 Logout -->
          <VListItem @click="logout" class="text-error">
            <template #prepend>
              <VIcon
                class="me-2"
                icon="bx-log-out"
                size="22"
              />
            </template>

            <VListItemTitle>Logout</VListItemTitle>
          </VListItem>
        </VList>
      </VMenu>
      <!-- !SECTION -->
    </VAvatar>
  </VBadge>
</template>
