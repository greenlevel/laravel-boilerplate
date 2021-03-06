<template>
  <div class="table-container">
    <b-row>
      <b-col md="4" class="mb-3">
        <b-form inline v-if="lengthChange">
          <label class="mr-2">{{ $t('labels.datatables.show_per_page') }}</label>
          <b-form-select :options="pageOptions" v-model="perPage" class="mr-2" @input="onContextChanged"></b-form-select>
          <label>{{ $t('labels.datatables.entries_per_page') }}</label>
        </b-form>
      </b-col>
      <b-col md="4" class="mb-3 text-center">
        <label class="mt-2" v-if="infos">{{ $t('labels.datatables.infos', { offset_start: perPage * (currentPage - 1) + 1, offset_end: perPage * currentPage, total: totalRows }) }}</label>
      </b-col>
      <b-col md="4" class="mb-3">
        <b-form inline v-if="search" class="d-flex justify-content-end">
          <label class="mr-2">{{ $t('labels.datatables.search') }}</label>
          <b-form-input v-model="searchQuery" @input="onContextChanged"></b-form-input>
        </b-form>
      </b-col>
    </b-row>
    <slot></slot>
    <b-row>
      <b-col md="4">
        <form class="form-inline" @submit.prevent="onBulkAction" v-if="actions">
          <div class="form-group form-group-sm">
            <select name="action" class="form-control mr-1" v-model="action">
              <option v-for="(action, value) in actions" :key="value" :value="value">{{ action }}</option>
            </select>
            <b-button type="submit" variant="danger">{{ $t('labels.validate') }}</b-button>
          </div>
        </form>
      </b-col>
      <b-col md="4">
        <b-pagination :total-rows="totalRows" :per-page="perPage" v-model="currentPage" v-if="paging && totalRows > perPage"
                      class="justify-content-center" @input="onContextChanged"></b-pagination>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  props: {
    lengthChange: {
      type: Boolean,
      default: true
    },
    paging: {
      type: Boolean,
      default: true
    },
    infos: {
      type: Boolean,
      default: true
    },
    search: {
      type: Boolean,
      default: true
    },
    searchRoute: {
      type: String,
      default: null
    },
    deleteRoute: {
      type: String,
      default: null
    },
    actionRoute: {
      type: String,
      default: null
    },
    actions: {
      type: Object,
      default: () => {}
    }
  },
  data () {
    return {
      currentPage: 1,
      perPage: 15,
      totalRows: 0,
      pageOptions: [ 5, 10, 15, 25, 50 ],
      searchQuery: null,
      selected: [],
      action: null
    }
  },
  mounted () {
    if (this.actions) {
      this.action = Object.keys(this.actions)[0]
    }
  },
  methods: {
    onContextChanged () {
      this.$emit('context-changed')
    },
    loadData (sortBy, sortDesc) {
      return axios.get(this.$app.route(this.searchRoute), {
        params: {
          page: this.currentPage,
          perPage: this.perPage,
          column: sortBy,
          direction: sortDesc ? 'desc' : 'asc',
          search: this.searchQuery
        }
      })
        .then((response) => {
          this.totalRows = response.data.total

          return response.data.data
        })
        .catch((error) => {
          this.$app.error(error)
          return []
        })
    },
    deleteRow (params) {
      window.swal({
        title: this.$t('labels.are_you_sure'),
        type: 'warning',
        showCancelButton: true,
        cancelButtonText: this.$t('buttons.cancel'),
        confirmButtonColor: '#dd4b39',
        confirmButtonText: this.$t('buttons.delete')
      }).then((result) => {
        if (result.value) {
          axios.delete(this.$app.route(this.deleteRoute, params))
            .then((response) => {
              this.onContextChanged()
              this.$app.noty[response.data.status](response.data.message)
            })
            .catch((error) => {
              this.$app.error(error)
            })
        }
      })
    },
    onBulkAction () {
      window.swal({
        title: this.$t('labels.are_you_sure'),
        type: 'warning',
        showCancelButton: true,
        cancelButtonText: this.$t('buttons.cancel'),
        confirmButtonColor: '#dd4b39',
        confirmButtonText: this.$t('buttons.confirm')
      }).then((result) => {
        if (result.value) {
          axios.post(this.$app.route(this.actionRoute), {
            action: this.action,
            ids: this.selected
          }).then((response) => {
            this.onContextChanged()
            this.$emit('bulk-action-success')
            this.$app.noty[response.data.status](response.data.message)
          }).catch((error) => {
            this.$app.error(error)
          })
        }
      })
    }
  }
}
</script>
