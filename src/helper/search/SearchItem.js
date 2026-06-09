export const searchItem = (data, text) => {

  const newData = data.filter((item) => {
    if (item && item?.mason_name && item?.mason_phone && item?.mason_aadhaar_no) {
      const itemData = `${item?.mason_name?.toUpperCase()} ${item?.product_name?.toUpperCase()} ${item?.mason_phone?.toUpperCase()} ${item?.mason_aadhaar_no?.toUpperCase()}`
      const textData = text.toUpperCase()
      return itemData.indexOf(textData) > -1
    } else if (item?.mason_name && item?.mason_phone && item?.mason_aadhaar_no == null) {
      const itemData = `${item?.mason_name.toUpperCase()} ${item?.mason_phone.toUpperCase()}`
      const textData = text.toUpperCase()
      return itemData.indexOf(textData) > -1
    }
  })
  return newData
}