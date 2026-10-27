<template>
  <view class="flex-col page">
    <view class="flex-col group">
      <view class="flex-col section_1">
        <image
          src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844631489950773.png"
          class="image_1"
        />
        <view class="flex-col items-center group_1">
          <text class="text">支付失败</text>
          <text class="text_1">支付失败请重新支付订单</text>
        </view>
      </view>
      <view class="flex-col group_2">
        <view class="flex-col items-center button">
          <text>重新支付</text>
        </view>
        <view class="flex-col items-center button_1">
          <text>返回</text>
        </view>
      </view>
    </view>
  </view>
</template>

<script>
  export default {
    data() {
      return {};
    },
  };
</script>

<style scoped lang="scss">
  .page {
    background-color: #ffffff;
    width: 100%;
    height: 100%;
    overflow-y: auto;
    .group {
      padding-bottom: 752rpx;
      flex: 1 1 auto;
      overflow-y: auto;
      .section_1 {
        padding: 46rpx 0 68rpx;
        background-color: rgb(243, 151, 53);
        .image_1 {
          align-self: center;
          width: 188rpx;
          height: 188rpx;
        }
        .group_1 {
          margin-top: 24rpx;
          .text {
            color: rgb(255, 255, 255);
            font-size: 36rpx;
            font-weight: 500;
            line-height: 50rpx;
            white-space: nowrap;
          }
          .text_1 {
            margin-top: 8rpx;
            color: rgb(255, 255, 255);
            font-size: 28rpx;
            line-height: 40rpx;
            white-space: nowrap;
          }
        }
      }
      .group_2 {
        margin-top: 80rpx;
        padding: 0 154rpx;
        .button {
          padding: 18rpx 0;
          color: rgb(255, 255, 255);
          font-size: 32rpx;
          font-weight: 500;
          line-height: 44rpx;
          white-space: nowrap;
          background-color: rgb(243, 151, 53);
          border-radius: 40rpx;
        }
        .button_1 {
          margin-top: 38rpx;
          padding: 16rpx 0;
          color: rgb(243, 151, 53);
          font-size: 32rpx;
          font-weight: 500;
          line-height: 44rpx;
          white-space: nowrap;
          border-radius: 40rpx;
          border: solid 2rpx rgb(243, 151, 53);
        }
      }
    }
  }
</style>