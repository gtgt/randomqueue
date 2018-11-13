<template>
  <div>
    <md-toolbar md-elevation="1">
      <h3 class="md-title" style="flex: 1">Title</h3>
      <form novalidate class="md-layout" @submit.prevent="validateJob">
          <md-field  :class="getValidationClass('theNumber')">
            <label for="the-number">The number</label>
            <md-input id="the-number" name="the-number" v-model="form.theNumber" :disabled="sending" :class="getValidationClass('theNumber')"></md-input>
            <span class="md-helper-text">Helper text</span>
            <span class="md-error" v-if="!$v.form.theNumber.numeric">The numeric value is required</span>
            <span class="md-error" v-else-if="!$v.form.theNumber.minValue">The value should be greater than zero.</span>
          </md-field>
          <md-progress-bar md-mode="indeterminate" v-if="sending" />
          <md-button type="submit" class="md-primary" :disabled="sending">Create job</md-button>
          <md-snackbar :md-active.sync="jobSent">The job was sent!</md-snackbar>
      </form>
    </md-toolbar>
    <md-table v-model="searched" md-sort="name" md-sort-order="asc" md-card md-fixed-header>
      <md-table-toolbar>
        <div class="md-toolbar-section-start">
          <h1 class="md-title">Log</h1>
        </div>

        <md-field md-clearable class="md-toolbar-section-end">
          <md-input placeholder="Search by name/id..." v-model="search" @input="searchOnTable"/>
        </md-field>
      </md-table-toolbar>

      <md-table-empty-state
        md-label="No log entry found"
        :md-description="`No log found for this '${search}' query. Try a different search term or produce some more log entries by creating a new job.`">
        <md-button class="md-primary md-raised" @click="newJob">Create New Job</md-button>
      </md-table-empty-state>

      <md-table-row slot="md-table-row" slot-scope="{ item }">
        <md-table-cell md-label="ID" md-sort-by="id" md-numeric>{{ item.id }}</md-table-cell>
        <md-table-cell md-label="Time" md-sort-by="time">{{ item.time }}</md-table-cell>
        <md-table-cell md-label="Level" md-sort-by="level">{{ item.level }}</md-table-cell>
        <md-table-cell md-label="Message" md-sort-by="message">{{ item.message }}</md-table-cell>
        <md-table-cell md-label="Context" md-sort-by="context">{{ item.context }}</md-table-cell>
      </md-table-row>
    </md-table>
  </div>
</template>

<script>
  import { validationMixin } from 'vuelidate';
  import {
    numeric,
    minValue
  } from 'vuelidate/lib/validators';

  const toLower = text => {
    return text.toString().toLowerCase();
  };

  const searchByName = (items, term) => {
    if (term) {
      return items.filter(item => toLower(item.message).includes(toLower(term)));
    }

    return items;
  };

  export default {
    name: 'App',
    mixins: [validationMixin],
    data: () => ({
      log: [],
      search: null,
      searched: [],
      form: {
        theNumber: null,
      },
      jobSent: false,
      sending: false,
    }),
    validations: {
      form: {
        theNumber: {
          numeric,
          minValue: minValue(1)
        }
      }
    },
    methods: {
      refreshLog() {
        this.$http.get('/log').then(response => {
          this.log = response.body;
          this.searchOnTable();
        }, response => {
          this.log = [];
          this.searched = [];
        });
      },
      newJob() {
        this.$el.querySelector('#the-number').focus();
      },
      searchOnTable() {
        this.searched = searchByName(this.log, this.search);
      },
      sendJob() {
        this.sending = true;

        window.setTimeout(() => {
          this.form.theNumber = null;
          this.sending = false;
          this.jobSent = true;
        }, 1500);
      },
      getValidationClass(fieldName) {
        const field = this.$v.form[fieldName];

        if (field) {
          return {
            'md-invalid': field.$invalid && field.$dirty
          };
        }
        return null;
      },
      validateJob() {
        this.$v.$touch();

        if (!this.$v.$invalid) {
          this.sendJob();
        }
      }
    },
    mounted() {
      this.refreshLog();
      window.setInterval(() => {
        this.refreshLog();
      }, 5000);
    }
  };
</script>

<style lang="scss" scoped>
  .md-table-cell:nth-child(4), .md-table-header:nth-child(4) {
    min-width: 400px;
  }
</style>
