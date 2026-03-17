<script setup>
import chartInfo from '@images/cards/chart-info.png'
import creditCardSuccess from '@images/cards/credit-card-success.png'
import creditCardWarning from '@images/cards/credit-card-warning.png'
import paypalError from '@images/cards/paypal-error.png'
import walletPrimary from '@images/cards/wallet-primary.png'

const transactions = [
  {
    amount: +82.6,
    paymentMethod: 'PayPal',
    description: 'Send money',
    icon: paypalError,
    color: 'error',
  },
  {
    paymentMethod: 'Wallet',
    amount: +270.69,
    description: 'Mac\'D',
    icon: walletPrimary,
    color: 'primary',
  },
  {
    amount: +637.91,
    paymentMethod: 'Transfer',
    description: 'Refund',
    icon: chartInfo,
    color: 'info',
  },
  {
    paymentMethod: 'Credit Card',
    amount: -838.71,
    description: 'Ordered Food',
    icon: creditCardSuccess,
    color: 'success',
  },
  {
    paymentMethod: 'Wallet',
    amount: +203.33,
    description: 'Starbucks',
    icon: walletPrimary,
    color: 'primary',
  },
  {
    paymentMethod: 'Mastercard',
    amount: -92.45,
    description: 'Ordered Food',
    icon: creditCardWarning,
    color: 'warning',
  },
]

const moreList = [
  {
    title: 'Share',
    value: 'Share',
  },
  {
    title: 'Refresh',
    value: 'Refresh',
  },
  {
    title: 'Update',
    value: 'Update',
  },
]
</script>

<template>
  <VCard title="Transactions">
    <template #append>
      <MoreBtn :menu-list="moreList" />
    </template>

    <VCardText>
      <VList class="card-list">
        <VListItem
          v-for="item in transactions"
          :key="item.paymentMethod"
        >
          <template #prepend>
            <VAvatar
              rounded
              variant="tonal"
              :color="item.color"
              :image="item.icon"
              size="40"
            />
          </template>

          <VListItemSubtitle>
            {{ item.paymentMethod }}
          </VListItemSubtitle>
          <VListItemTitle>
            {{ item.description }}
          </VListItemTitle>

          <template #append>
            <VListItemAction>
              <span class="me-2">{{ item.amount > 0 ? `+$${Math.abs(item.amount)}` : `-$${Math.abs(item.amount)}` }}</span>
              <span class="text-disabled">USD</span>
            </VListItemAction>
          </template>
        </VListItem>
      </VList>
    </VCardText>
  </VCard>
</template>

<style lang="scss" scoped>
  .card-list {
    --v-card-list-gap: 1.5rem;
  }
</style>
