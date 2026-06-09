import React, { createContext, useContext, useState } from 'react';

const DeleteVisibleContext = createContext({
  deleteVisible: false,
  setDeleteVisible: () => {},
});

export const DeleteVisibleProvider = ({ children }) => {
  const [deleteVisible, setDeleteVisible] = useState(false);
  return (
    <DeleteVisibleContext.Provider value={{ deleteVisible, setDeleteVisible }}>
      {children}
    </DeleteVisibleContext.Provider>
  );
};

export const useDeleteVisible = () => useContext(DeleteVisibleContext);