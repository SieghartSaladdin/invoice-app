<template>
  <div class="container">
     <!--==================== INVOICE LIST ====================-->
     <div class="invoices">
        
        <div class="card__header">
            <div>
                <h2 class="invoice__title">Invoices</h2>
            </div>
            <div>
                <a class="btn btn-secondary" @click="newInvoice">
                    New Invoice
                </a>
            </div>
        </div>

        <div class="table card__content">
            <div class="table--filter">
                <span class="table--filter--collapseBtn ">
                    <i class="fas fa-ellipsis-h"></i>
                </span>
                <div class="table--filter--listWrapper">
                    <ul class="table--filter--list">
                        <li>
                            <p class="table--filter--link table--filter--link--active">
                                All
                            </p>
                        </li>
                        <li>
                            <p class="table--filter--link ">
                                Paid
                            </p>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="table--search">
                <div class="table--search--wrapper">
                    <select class="table--search--select" name="" id="">
                        <option value="">Filter</option>
                    </select>
                </div>
                <div class="relative">
                    <i class="table--search--input--icon fas fa-search "></i>
                    <input class="table--search--input" type="text" placeholder="Search invoice"
                    v-model="searchInvoice" @input="search">
                </div>
            </div>
            <div class="table--heading">
                <p>ID</p>
                <p>Date</p>
                <p>Number</p>
                <p>Customer</p>
                <p>Due Date</p>
                <p>Total</p>
            </div>

            <!-- item 1 -->
            <div class="table--items" v-for="item in invoices" :key="item.id">
                <p class="table--items--transactionId" style="cursor: pointer;" @click="onShow(item.id)">{{ item.id }}</p>
                <p>{{item.date}}</p>
                <p>#{{ item.number }}</p>
                <p v-if="item.customer">{{ item.customer.firstname }}</p>
                <p v-else></p>
                <p>{{ item.due_date }}</p>
                <p>${{ item.total }}</p>
            </div>
        </div>
        
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import {useRouter} from "vue-router"

export default {
  data(){
    return {
      invoices: [],
      searchInvoice: '',
      router: useRouter()
    }
  },
  mounted() {
    axios.get('/api/get_all_invoice')
      .then(response => {
        // Mengambil data dari properti `data` di objek response
        const invoice = response.data.invoice;
        this.invoices = invoice;
      })
      .catch(error => {
        console.error('Terjadi kesalahan saat mengambil data invoice:', error);
    });
  },
  methods: {
    search() {
      axios.get('/api/search_invoice', {
        params: {
          s: this.searchInvoice
        }
      })
      .then(response => {
        this.invoices = response.data.invoices;
      })
      .catch(error => {
        console.error(error);
      });
    },
    onShow(id) {
        this.router.push('/invoice/show/'+id)
    },
    newInvoice() {
      axios.get('/api/create_invoice')
      .then(response => {
        this.router.push('/invoice/new')
      })
      .catch(error => {
        console.error(error);
      });
    }
  }
}
</script>

<style>

</style>