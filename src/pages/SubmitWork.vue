<template>
  <q-page class="flex flex-center">
    <div class="row full-width justify-center">
      <div class="col-5">
        <div class="text-center">
          <strong>Work Form</strong>
        </div>
        <q-form @submit="submitWork" class="q-gutter-md q-mt-md">
          <div class="">
            <q-select
              v-model="work.work_type"
              :options="['Plan', 'Status']"
              label="Work Type"
              filled
            />
          </div>
          <div class="" v-if="work.work_type === 'Status'">
            <q-select
              v-model="work_plan_id"
              :options="work_plans"
              label="Select Work Plan"
              option-value="id"
              option-label="subject"
              emit-value
              map-options
              filled
            />
          </div>
          <div class="">
            <q-input filled v-model="work.start_date" label="Start Time">
              <template v-slot:prepend>
                <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy
                    cover
                    transition-show="scale"
                    transition-hide="scale"
                  >
                    <q-date v-model="work.start_date" mask="YYYY-MM-DD HH:mm">
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

              <template v-slot:append>
                <q-icon name="access_time" class="cursor-pointer">
                  <q-popup-proxy
                    cover
                    transition-show="scale"
                    transition-hide="scale"
                  >
                    <q-time
                      v-model="work.start_date"
                      mask="YYYY-MM-DD HH:mm"
                      format24h
                    >
                      <div class="row items-center justify-end">
                        <q-btn
                          v-close-popup
                          label="Close"
                          color="primary"
                          flat
                        />
                      </div>
                    </q-time>
                  </q-popup-proxy>
                </q-icon>
              </template>
            </q-input>
          </div>

          <div class="">
            <q-input v-model="work.hours" type="text" label="Hours" filled />
          </div>
          <div class="">
            <q-editor
              v-model="work.message_body"
              :definitions="{
                bold: { label: 'Bold', icon: null, tip: '' },
              }"
            />
          </div>
          <div class="">
            <q-input
              v-model="work.summary"
              type="text"
              label="Summary"
              filled
            />
          </div>
          <q-file outlined v-model="work.attachments" multiple>
            <template v-slot:prepend>
              <q-icon name="attach_file" />
            </template>
          </q-file>
          <q-toggle v-model="work.send" label="Send To Kali" />
          <q-toggle v-model="work.save" label="Save Only" />
          <q-btn
            color="primary"
            icon="check"
            label="Submit"
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
  name: "SubmitWork",
  data() {
    return {
      work_plans: [],
      work_plan_id: null,
      work: {
        send: false,
        work_type: null,
        start_date: null,
        hours: null,
        attachments: null,
        message_body: null,
        summary: null,
        save: false,
      },
    };
  },
  computed: {
    workPlan() {
      return this.work_plans.find((p) => +p.id === +this.work_plan_id);
    },
  },
  watch: {
    workPlan(plan) {
      if (plan) {
        this.work.message_body = plan.original_content;
        this.work.summary = plan.summary;
        this.work.start_date = plan.start_date;
        this.work.hours = plan.hours;
      }
      return plan;
    },
  },
  mounted() {
    this.getWorkPlans();
  },
  methods: {
    submitWork() {
      const yes = confirm(
        "Are you sure everything is valid, summary, date etc.?"
      );
      if (!yes) {
        return;
      }
      const fd = new FormData();

      fd.append("send", this.work.send);
      fd.append("start_date", this.work.start_date);
      fd.append("hours", this.work.hours);
      fd.append("work_type", this.work.work_type);
      fd.append("message_body", this.work.message_body);
      fd.append("summary", this.work.summary);
      fd.append("save_only", this.work.save);

      if (this.work.attachments) {
        this.work.attachments.forEach((f) => {
          fd.append("attachments[]", f);
        });
      }
      this.$q.loading.show();
      api
        .post("api/send_work", fd)
        .then((res) => {
          console.log(res.data);
          this.$q.loading.hide();
          this.$q.notify({ message: "Submitted", type: "positive" });
        })
        .catch((err) => {
          this.$q.loading.hide();
          this.$q.notify({ message: "Failed", type: "negative" });
        });
    },
    getWorkPlans() {
      api
        .get("api/work_plans")
        .then((res) => {
          console.log(res.data);
          this.work_plans = res.data;
        })
        .catch((err) => {
          this.$q.loading.hide();
          this.$q.notify({
            message: "Failed to retrieve work plans",
            type: "negative",
          });
        });
    },
  },
});
</script>
