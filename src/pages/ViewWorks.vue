<template>
  <q-page class="flex flex-start q-mt-lg">
    <div class="row full-width justify-center">
      <div class="col-5">
        <div class="text-center">
          <strong>Select date range for invoice</strong>
        </div>
        <q-form @submit="downloadInvoice" class="q-gutter-md q-mt-md">
          <div class="">
            <q-input filled v-model="date.start" label="Start Date">
              <template v-slot:prepend>
                <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy
                    cover
                    transition-show="scale"
                    transition-hide="scale"
                  >
                    <q-date v-model="date.start" mask="YYYY-MM-DD">
                      <div class="row items-center justify-end">
                        <q-btn
                          v-close-popup
                          label="Close"
                          color="primary"
                          flat
                        />
                      </div>
                    </q-date>
                  </q-popup-proxy>
                </q-icon>
              </template>
            </q-input>
          </div>
          <div class="">
            <q-input filled v-model="date.end" label="End Date">
              <template v-slot:prepend>
                <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy
                    cover
                    transition-show="scale"
                    transition-hide="scale"
                  >
                    <q-date v-model="date.end" mask="YYYY-MM-DD">
                      <div class="row items-center justify-end">
                        <q-btn
                          v-close-popup
                          label="Close"
                          color="primary"
                          flat
                        />
                      </div>
                    </q-date>
                  </q-popup-proxy>
                </q-icon>
              </template>
            </q-input>
          </div>
          <q-btn
            color="primary"
            icon="download"
            label="Download"
            type="submit"
            class="float-right"
          />
        </q-form>
      </div>
    </div>
  </q-page>
</template>

<script>
import { defineComponent } from "vue";
import { api } from "../boot/axios";
export default defineComponent({
  name: "ViewWorks",
  data() {
    return {
      date: {
        start: null,
        end: null,
      },
    };
  },
  methods: {
    downloadInvoice() {
      this.$q.loading.show();
      api
        .post("api/generate_invoice", this.date)
        .then((res) => {
          this.$q.loading.hide();
          this.$q.notify({ message: "Success", type: "positive" });
          const url = res.data;
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "invoice.xlsx"); //or any other extension
          document.body.appendChild(link);
          link.click();
        })
        .catch((err) => {
          this.$q.loading.hide();
          this.$q.notify({ message: "Failed", type: "negative" });
        });
    },
  },
});
</script>
