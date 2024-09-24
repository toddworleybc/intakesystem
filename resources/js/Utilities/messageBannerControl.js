
import { reactive } from "vue";

const messageBannerControl = reactive({
    'status': null,
    'type' : null,
    'message' : null,
    'display' : function (data) {
        this.status = data.status;
        this.type = data.type;
        this.message = data.message
    }
}); 

export default messageBannerControl;