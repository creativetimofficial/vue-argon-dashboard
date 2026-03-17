<script setup>
const desserts = [
  {
    dessert: 'Frozen Yogurt',
    calories: 159,
    fat: 6,
    carbs: 24,
    protein: 4,
  },
  {
    dessert: 'Ice cream sandwich',
    calories: 237,
    fat: 6,
    carbs: 24,
    protein: 4,
  },
  {
    dessert: 'Eclair',
    calories: 262,
    fat: 6,
    carbs: 24,
    protein: 4,
  },
  {
    dessert: 'Cupcake',
    calories: 305,
    fat: 6,
    carbs: 24,
    protein: 4,
  },
  {
    dessert: 'Gingerbread',
    calories: 356,
    fat: 6,
    carbs: 24,
    protein: 4,
  },
]
</script>

<template>
  <VTable
    theme="dark"
    class="rounded-0"
  >
    <thead>
      <tr>
        <th>
          Desserts (100g Servings)
        </th>
        <th class="text-uppercase">
          calories
        </th>
        <th class="text-uppercase">
          Fat(g)
        </th>
        <th class="text-uppercase">
          Carbs(g)
        </th>
        <th class="text-uppercase">
          protein(g)
        </th>
      </tr>
    </thead>

    <tbody>
      <tr
        v-for="item in desserts"
        :key="item.dessert"
      >
        <td>
          {{ item.dessert }}
        </td>
        <td>
          {{ item.calories }}
        </td>
        <td>
          {{ item.fat }}
        </td>
        <td>
          {{ item.carbs }}
        </td>
        <td>
          {{ item.protein }}
        </td>
      </tr>
    </tbody>
  </VTable>
</template>
