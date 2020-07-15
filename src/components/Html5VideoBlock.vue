<template>
  <div>
    <figure>
      <template v-if="attrs.src">
        <div
          ref="element"
          :style="style"
          :data-responsive="attrs.ratio"
          class="k-editor-html5video-block-wrapper"
          tabindex="0"
          @keydown.delete="$emit('remove')"
          @keydown.enter="$emit('append')"
          @keydown.up="$emit('prev')"
          @keydown.down="$emit('next')"
        >
        <video ref="video" :src="attrs.src" :key="attrs.src" @dblclick="selectFile" @loadedmetadata="onLoad">
        </div>
        <figcaption>
          <k-editable
            :content="attrs.caption"
            :breaks="true"
            :placeholder="$t('editor.blocks.html5video.caption.placeholder') + '…'"
            :spellcheck="spellcheck"
            @prev="focus"
            @shiftTab="focus"
            @tab="$emit('next', $event)"
            @next="$emit('next', $event)"
            @split="$emit('append')"
            @enter="$emit('append')"
            @input="caption"
          />
        </figcaption>
      </template>
      <template v-else>
        <k-dropzone
          ref="element"
          class="k-editor-html5video-block-placeholder"
          tabindex="0"
          @keydown.native.delete="$emit('remove')"
          @keydown.native.enter="$emit('append')"
          @keydown.native.up.prevent="$emit('prev')"
          @keydown.native.down.prevent="$emit('next')"
          @drop="onDrop"
        >
          <k-button icon="upload" @click="uploadFile" @keydown.enter.native.stop>{{ $t('editor.blocks.html5video.upload') }}</k-button>
          {{ $t('editor.blocks.html5video.or') }}
          <k-button icon="video" @click="selectFile" @keydown.enter.native.stop>{{ $t('editor.blocks.html5video.select') }}</k-button>
        </k-dropzone>
      </template>
    </figure>

    <k-files-dialog ref="fileDialog" @submit="insertFile($event)" />
    <k-upload ref="fileUpload" @success="insertUpload" />

    <k-dialog ref="settings" @submit="saveSettings" size="medium">
      <k-form :fields="fields" v-model="attrs" @submit="saveSettings" />
    </k-dialog>

  </div>
</template>

<script>
export default {
  icon: "video",
    label: "HTML5 Video",
  props: {
    attrs: {
      type: Object,
    },
    endpoints: Object,
    spellcheck: Boolean
  },
  data() {
    return {
      defaults: {
        lazyloading: true,
        autoplay: true,
        playsinline: true,
        muted: true,
        loop: true,
        controls: false,
      }
    }
  },
  created() {
    console.log(this.defaults);
    this.$emit("input", {
      attrs: {
        ...this.defaults,
        ...this.attrs
      }
    });
  },
  computed: {
    style() {
      if (this.attrs.ratio) {
        return 'padding-bottom:' + 100 / this.attrs.ratio + '%';
      }
    },
    fields() {
      return {
        alt: {
          label: this.$t('editor.blocks.html5video.alt.label'),
          type: "text",
          icon: "text"
        },
        css: {
          label: this.$t('editor.blocks.html5video.css.label'),
          type: "text",
          icon: "code",
        },
        lazyloading: {
          label: this.$t('editor.blocks.html5video.lazyloading.label'),
          type: "toggle",
        },
        autoplay: {
          label: this.$t('editor.blocks.html5video.autoplay.label'),
          type: "toggle",
        },
        playsinline: {
          label: this.$t('editor.blocks.html5video.playsinline.label'),
          type: "toggle",
        },
        loop: {
          label: this.$t('editor.blocks.html5video.loop.label'),
          type: "toggle",
        },
        muted: {
          label: this.$t('editor.blocks.html5video.muted.label'),
          type: "toggle",
        },
        controls: {
          label: this.$t('editor.blocks.html5video.controls.label'),
          type: "toggle",
        },
      };
    }
  },
  methods: {
    caption(html) {
      this.input({
        caption: html
      });
    },
    edit() {
      if (this.attrs.guid) {
        this.$router.push(this.attrs.guid);
      }
    },
    focus() {
      if (this.attrs.src) {
        this.$refs.element.focus();
      } else {
        this.$refs.element.$el.focus();
      }
    },
    input(data) {
      this.$emit("input", {
        attrs: {
          ...this.attrs,
          ...data
        }
      });
    },
    fetchFile(link) {
      this.$api.get(link).then(response => {
        this.input({
          guid: response.link,
          src: response.url,
          id: response.id,
          ratio: response.dimensions.ratio
        });
      });
    },
    insertFile(files) {
      const file = files[0];
      this.fetchFile(file.link);
    },
    insertUpload(files, response) {
      this.fetchFile(response[0].link);
      this.$events.$emit("file.create");
      this.$events.$emit("model.update");
      this.$store.dispatch("notification/success", ":)");
    },
    menu() {

      if (this.attrs.src) {
        return [
          {
            icon: "open",
            label: this.$t("editor.blocks.html5video.open.browser"),
            click: this.open
          },
          {
            icon: "edit",
            label: this.$t("editor.blocks.html5video.open.panel"),
            click: this.edit,
            disabled: !this.attrs.guid
          },
          {
            icon: "cog",
            label: this.$t("editor.blocks.html5video.settings"),
            click: this.$refs.settings.open
          },
          {
            icon: "video",
            label: this.$t("editor.blocks.html5video.replace"),
            click: this.replace
          },
        ];
      } else {
        return [];
      }
    },
    open() {
      window.open(this.attrs.src);
    },
    onDrop(files) {
      this.$refs.fileUpload.drop(files, {
        url: window.panel.api + "/" + this.endpoints.field + "/upload",
        multiple: false,
        accept: "video/*"
      });
    },
    onLoad() {
      const video = this.$refs.video;
      if (!this.attrs.ratio && video && video.videoWidth && video.videoHeight) {
        this.input({
          ratio: video.videoWidth / video.videoHeight
        });
      }
    },
    replace() {
      this.$emit("input", {
        attrs: {}
      });
    },
    selectFile() {
      this.$refs.fileDialog.open({
        endpoint: this.endpoints.field + "/files",
        multiple: false,
        selected: [this.attrs.id]
      });
    },
    settings() {
      this.$refs.settings.open();
    },
    saveSettings() {
      this.$refs.settings.close();
      this.input(this.attrs);
    },
    uploadFile() {
      this.$refs.fileUpload.open({
        url: window.panel.api + "/" + this.endpoints.field + "/upload",
        multiple: false,
        accept: "video/*"
      });
    },
  }
};
</script>

<style>

.k-editor-html5video-block {
  padding-top: 1.5rem;
  padding-bottom: 1.5rem;
}
.k-editor-html5video-block figure {
  line-height: 0;
}
.k-editor-html5video-block-wrapper video {
  width: 100%;
}
.k-editor-html5video-block-wrapper[data-responsive] video {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  object-fit: contain;
  height: 100%;
}
.k-editor-html5video-block-wrapper[data-responsive] {
  position: relative;
  padding-bottom: 66.66%;
  background: var(--color-dark);

}
.k-editor-html5video-block-wrapper:focus {
  outline: 0;
}
.k-editor-html5video-block-wrapper:focus video {
  outline: 2px solid var(--color-focus-outline);
  outline-offset: 2px;
}
.k-editor-html5video-block figcaption {
  display: block;
  margin-top: .75rem;
}
.k-editor-html5video-block .k-editable-placeholder,
.k-editor-html5video-block .ProseMirror {
  text-align: center;
  font-size: .875rem;
  line-height: 1.5em;
}
.k-editor-html5video-block-placeholder {
  display: flex;
  line-height: 1;
  justify-content: center;
  align-items: center;
  font-style: italic;
  font-size: .875rem;
  width: 100%;
  background: var(--color-background);
  border: 1px solid transparent;
  border-radius: 4px;
  text-align: center;
  color: var(--color-text-lighter);
}
.k-editor-html5video-block-placeholder:focus {
  outline: 2px solid var(--color-focus-outline);
  outline-offset: 2px;
}
.k-editor-html5video-block-placeholder .k-button {
  padding: .75rem;
  display: flex;
  align-items: center;
  color: var(--color-black);
  margin: 0 .5rem;
}
.k-editor-html5video-block .k-editor-block-options {
  top: 20px;
}

</style>
