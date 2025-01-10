"use client";

import { ResponsiveModal } from "@/Components/ResponsiveModal";

import { useEditUserModal } from "../hooks/use-edit-user-modal";
import { EditUserWrapper } from "./edit-user-modal-wrapper";

export const EditUserModal = () => {
  const { userId, close } = useEditUserModal();

  return (
    <ResponsiveModal open={!!userId} onOpenChange={close}>
      {userId && <EditUserWrapper id={userId} onCancel={close} />}
    </ResponsiveModal>
  );
};
