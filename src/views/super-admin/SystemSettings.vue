<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
              <h6>{{ $t('settings.doc_help_settings') }}</h6>
              <button class="btn btn-primary btn-sm" @click="saveSettings" :disabled="loading">
                <i v-if="loading" class="fas fa-spinner fa-spin me-1"></i>
                {{ $t('settings.save_changes') }}
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-control-label">{{ $t('settings.help_title_label') }}</label>
                  <input id="help_title" name="help_title" class="form-control" type="text" v-model="settings.help_title" :placeholder="$t('settings.help_title_placeholder')" />
                </div>
                <div class="form-group">
                  <label class="form-control-label">{{ $t('settings.short_desc_label') }}</label>
                  <input id="help_description" name="help_description" class="form-control" type="text" v-model="settings.help_description" :placeholder="$t('settings.short_desc_placeholder')" />
                </div>
                <div class="form-group">
                  <label class="form-control-label">{{ $t('settings.docs_link_label') }}</label>
                  <input id="docs_link" name="docs_link" class="form-control" type="url" v-model="settings.docs_link" placeholder="https://..." />
                </div>
                <div class="form-group">
                  <label class="form-control-label">{{ $t('settings.youtube_link_label') }}</label>
                  <input id="youtube_link" name="youtube_link" class="form-control" type="url" v-model="settings.youtube_link" placeholder="https://youtube.com/..." />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Main Domain Card -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
              <h6>{{ $t('settings.main_domain_config') }}</h6>
              <button class="btn btn-primary btn-sm" @click="saveDomainSettings" :disabled="domainLoading">
                <i v-if="domainLoading" class="fas fa-spinner fa-spin me-1"></i>
                {{ $t('settings.save_domain') }}
              </button>
            </div>
          </div>
          <div class="card-body">
            <p class="text-sm text-secondary mb-3">{{ $t('settings.main_domain_desc') }} {{ $t('settings.example_domain') }} <strong>isp.{{ domainSettings.base_domain }}</strong>).</p>
            <div class="form-group">
              <label class="form-control-label">{{ $t('settings.base_domain_label') }}</label>
              <input id="base_domain" name="base_domain" class="form-control" type="text" v-model="domainSettings.base_domain" :placeholder="$t('settings.base_domain_placeholder')" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import notify from "@/utils/notify";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const loading = ref(false);
const settings = ref({
  help_title: 'Need Help ?',
  help_description: 'Please check our docs',
  docs_link: '',
  youtube_link: '',
  social_media: []
});

const fetchSettings = async () => {
  try {
    const res = await api.get('/super-admin/settings/documentation');
    if (res.data.settings) {
      settings.value = res.data.settings;
    }
  } catch (err) {
    console.error('Failed to fetch documentation settings', err);
  }
};

const saveSettings = async () => {
  loading.value = true;
  try {
    await api.post('/super-admin/settings/documentation', settings.value);
    notify('success', t('settings.save_success'));
  } catch (err) {
    notify('error', t('settings.save_failed'));
  } finally {
    loading.value = false;
  }
};

onMounted(fetchSettings);

const domainLoading = ref(false);
const domainSettings = ref({ base_domain: 'yourdomain.com' });

const fetchDomainSettings = async () => {
  try {
    const res = await api.get('/super-admin/settings/main-domain');
    if (res.data.settings) domainSettings.value = res.data.settings;
  } catch (err) {
    console.error('Failed to fetch domain settings', err);
  }
};

const saveDomainSettings = async () => {
  domainLoading.value = true;
  try {
    await api.post('/super-admin/settings/main-domain', domainSettings.value);
    notify('success', t('settings.domain_save_success'));
  } catch (err) {
    notify('error', t('settings.domain_save_failed'));
  } finally {
    domainLoading.value = false;
  }
};

onMounted(async () => {
  await Promise.all([fetchSettings(), fetchDomainSettings()]);
});
</script>
