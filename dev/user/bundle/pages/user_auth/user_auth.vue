<template>
  <view class="flex-col page">
    <view class="flex-col group">
      <view class="justify-between list-item">
        <view class="flex-row">
          <image
            src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844645327739265.png"
            class="image_1 image_2"
          />
          <view class="flex-col group_2">
            <view class="flex-row">
              <text class="text_1">实名认证</text>
              <view class="right-text-wrapper flex-col items-center">
                <text>已认证</text>
              </view>
            </view>
            <text class="text_3">已提供有效身份证证明</text>
          </view>
        </view>
        <text class="text_4">联网验证</text>
      </view>
      <view class="list-item_1 justify-between">
        <view class="flex-row">
          <image
            src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844647148495481.png"
            class="image_1 image_3"
          />
          <view class="right-group flex-col">
            <view class="flex-row">
              <text class="text_5">人脸识别</text>
              <view class="right-text-wrapper flex-col items-center">
                <text>已认证</text>
              </view>
            </view>
            <text class="text_8">已完成人脸识别验证本人身份</text>
          </view>
        </view>
        <text class="text_10">完成识别</text>
      </view>
      <view class="list-item_1 justify-between">
        <view class="flex-row">
          <image
            src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844645822214454.png"
            class="image_1 image_4"
          />
          <view class="right-group flex-col">
            <view class="flex-row">
              <text class="text_5">安全健康</text>
              <view class="right-text-wrapper flex-col items-center">
                <text>已认证</text>
              </view>
            </view>
            <text class="text_8">定期检查 健康达标</text>
          </view>
        </view>
        <text class="text_10">完成审核</text>
      </view>
      <view class="list-item_1 justify-between">
        <view class="flex-row">
          <image
            src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844645994432839.png"
            class="image_1 image_5"
          />
          <view class="right-group flex-col">
            <view class="flex-row">
              <text class="text_5">资格认证</text>
              <view class="right-text-wrapper flex-col items-center">
                <text>已认证</text>
              </view>
            </view>
            <text class="text_8">技师职业资格证书</text>
          </view>
        </view>
        <text class="text_10">完成审核</text>
      </view>
      <view class="flex-col section_4">
        <view class="flex-row group_10">
          <view class="section_5"> </view>
          <text class="text_20">技师资格证</text>
        </view>
        <view class="flex-col items-center text-wrapper">
          <text>技师资格证</text>
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
    background-color: #f8f8f8;
    width: 100%;
    height: 100%;
    overflow-y: auto;
    .group {
      flex: 1 1 auto;
      overflow-y: auto;
      .list-item {
        padding: 32rpx 32rpx 34rpx;
        background-color: rgb(255, 255, 255);
        .text_4 {
          align-self: center;
          color: rgb(24, 181, 103);
          font-size: 28rpx;
          line-height: 40rpx;
          white-space: nowrap;
        }
        .image_2 {
          margin-top: 4rpx;
        }
        .group_2 {
          margin-left: 16rpx;
          .text_3 {
            margin-top: 8rpx;
            color: rgb(102, 102, 102);
            font-size: 24rpx;
            line-height: 34rpx;
            white-space: nowrap;
          }
          .text_1 {
            color: rgb(0, 0, 0);
            font-size: 32rpx;
            font-weight: 500;
            line-height: 44rpx;
            white-space: nowrap;
          }
        }
      }
      .list-item_1 {
        padding: 34rpx 32rpx;
        background-color: rgb(255, 255, 255);
        .text_10 {
          align-self: center;
          color: rgb(24, 181, 103);
          font-size: 28rpx;
          line-height: 40rpx;
          white-space: nowrap;
        }
        .image_3 {
          margin-top: 4rpx;
        }
        .right-group {
          margin-left: 16rpx;
          .text_8 {
            margin-top: 8rpx;
            color: rgb(102, 102, 102);
            font-size: 24rpx;
            line-height: 34rpx;
            white-space: nowrap;
          }
          .text_5 {
            color: rgb(0, 0, 0);
            font-size: 32rpx;
            font-weight: 500;
            line-height: 44rpx;
            white-space: nowrap;
          }
        }
        .image_4 {
          margin-bottom: 4rpx;
        }
        .image_5 {
          margin-bottom: 4rpx;
        }
      }
      .section_4 {
        padding: 0 32rpx 366rpx;
        background-color: rgb(255, 255, 255);
        .group_10 {
          padding: 24rpx 0 32rpx;
          color: rgb(0, 0, 0);
          font-size: 32rpx;
          font-weight: 500;
          line-height: 44rpx;
          white-space: nowrap;
          .section_5 {
            margin: 6rpx 0;
            background-color: rgb(24, 181, 103);
            border-radius: 4rpx;
            width: 8rpx;
            height: 32rpx;
          }
          .text_20 {
            margin-left: 16rpx;
          }
        }
        .text-wrapper {
          padding: 158rpx 0;
          color: rgba(24, 181, 103, 0.32);
          font-size: 32rpx;
          font-weight: 500;
          line-height: 44rpx;
          white-space: nowrap;
          background-color: rgba(24, 181, 103, 0.12);
          border-radius: 24rpx;
        }
      }
      .image_1 {
        width: 80rpx;
        height: 80rpx;
      }
      .right-text-wrapper {
        margin: 4rpx 0 4rpx 16rpx;
        color: rgb(24, 181, 103);
        font-size: 24rpx;
        line-height: 34rpx;
        white-space: nowrap;
        padding-top: 2rpx;
        background-color: rgba(24, 181, 103, 0.2);
        border-radius: 8rpx;
        width: 96rpx;
        height: 36rpx;
      }
    }
  }
</style>