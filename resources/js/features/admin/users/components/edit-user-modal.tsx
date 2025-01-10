"use client";

import { ResponsiveModal } from "@/Components/ResponsiveModal";

import { useEditUserModal } from "../hooks/use-edit-user-modal";

export const EditUserModal = () => {
  const { userId, close } = useEditUserModal();

  return (
    <ResponsiveModal open={!!userId} onOpenChange={close}>
      {userId && userId}
    </ResponsiveModal>
  );
};
